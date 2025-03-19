import os
import sys
sys.path.append("C:/Users/Rina Mae/Desktop/Python 1")

#preview
from flask import Flask, render_template, request, jsonify, send_from_directory, url_for
import pytesseract
from pdf2image import convert_from_path
from flask import Flask, request, jsonify, url_for
from PIL import Image


#delete
from flask import Flask, request, jsonify
from dLt_docinfo import rmv_document  # This should work now!

from werkzeug.utils import secure_filename
import psycopg2
from psycopg2.extras import RealDictCursor
from flask import Flask, render_template, request, redirect, url_for, flash, session
from flask_bcrypt import Bcrypt
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from flask import send_file



app = Flask(__name__)
app.secret_key = "supersecretkey"  # Change this in production
bcrypt = Bcrypt(app)
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = "login"



# Database connection
def get_db_connection():
    return psycopg2.connect(
        dbname="BAC",
        user="postgres",
        password="652018",
        host="localhost",
        port="5432",
        cursor_factory=RealDictCursor
    )

# User class
class User(UserMixin):
    def __init__(self, id, username, email):
        self.id = id
        self.username = username
        self.email = email

@login_manager.user_loader
def load_user(user_id):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("SELECT * FROM users WHERE id = %s", (user_id,))
    user = cur.fetchone()
    cur.close()
    conn.close()
    return User(user['id'], user['username'], user['email']) if user else None

# Home Page
@app.route('/')
def home():
    return render_template("index.php")

# Registration Page
@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        email = request.form['email']
        password = bcrypt.generate_password_hash(request.form['password']).decode('utf-8')

        conn = get_db_connection()
        cur = conn.cursor()
        try:
            cur.execute("INSERT INTO users (username, email, password) VALUES (%s, %s, %s)", 
                        (username, email, password))
            conn.commit()
            flash("Registration successful! You can now log in.", "success")
            return redirect(url_for('login'))
        except psycopg2.IntegrityError:
            conn.rollback()
            flash("Username or email already exists.", "danger")
        finally:
            cur.close()
            conn.close()

    return render_template("register.php")

# Login Page
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']

        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("SELECT * FROM users WHERE email = %s", (email,))
        user = cur.fetchone()
        cur.close()
        conn.close()

        if user and bcrypt.check_password_hash(user['password'], password):
            login_user(User(user['id'], user['username'], user['email']))
            flash("Login successful!", "success")
            return redirect(url_for('dashboard'))
        else:
            flash("Invalid email or password", "danger")
  

    return render_template("login.php")


#Dashboard
@app.route('/dashboard')
@login_required
def dashboard():
    conn = get_db_connection()
    cur = conn.cursor()

    # Fetch document details and group files
    cur.execute("""
        SELECT d.id, d.project_title, d.contractor, d.date_ntp, d.proprietress,
               COALESCE(ARRAY_AGG(f.filename) FILTER (WHERE f.filename IS NOT NULL), '{}') AS filenames
        FROM documents d
        LEFT JOIN files f ON d.id = f.document_id
        WHERE d.user_id = %s
        GROUP BY d.id, d.project_title, d.contractor, d.date_ntp, d.proprietress
        ORDER BY d.date_ntp DESC
    """, (current_user.id,))
    documents = cur.fetchall()

    # Fetch folders and files
    cur.execute("SELECT folder_name FROM files WHERE user_id = %s GROUP BY folder_name", (current_user.id,))
    user_folders = [row['folder_name'] for row in cur.fetchall()]

    cur.close()
    conn.close()

    return render_template("dashboard.php", username=current_user.username, 
                           user_folders=user_folders, documents=documents)



#Insert a New Document
def insert_document(user_id, project_title, contractor, date_ntp, proprietress):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO documents (user_id, project_title, contractor, date_ntp, proprietress)
        VALUES (%s, %s, %s, %s, %s) RETURNING id
    """, (user_id, project_title, contractor, date_ntp, proprietress))
    
    document_id = cur.fetchone()['id']
    conn.commit()
    cur.close()
    conn.close()
    return document_id

# Insert Files Linked to a Document
def insert_files(document_id, file_list):
    conn = get_db_connection()
    cur = conn.cursor()
    for file in file_list:
        cur.execute("""
            INSERT INTO files (document_id, file_name, file_path)
            VALUES (%s, %s, %s)
        """, (document_id, file['filename'], file['filepath']))
    
    conn.commit()
    cur.close()
    conn.close()

#Fetch Documents with Associated Files

def get_documents(user_id):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        SELECT d.id, d.project_title, d.contractor, d.date_ntp, d.proprietress, 
               json_agg(json_build_object('id', f.id, 'file_name', f.file_name, 'file_path', f.file_path)) AS files
        FROM documents d
        LEFT JOIN files f ON d.id = f.document_id
        WHERE d.user_id = %s  -- Filter by logged-in user
        GROUP BY d.id
        ORDER BY d.created_at DESC
    """, (user_id,))
    
    documents = cur.fetchall()
    cur.close()
    conn.close()
    return documents


#upload_document

@app.route('/upload_document', methods=['POST'])
@login_required
def upload_document():
    if request.method == 'POST':
        project_title = request.form['project_title']
        contractor = request.form['contractor']
        date_ntp = request.form['date_ntp']
        proprietress = request.form['proprietress']
        files = request.files.getlist('files')  # Get multiple files

        conn = get_db_connection()
        cur = conn.cursor()

        # Insert document details into the database
        cur.execute("""
            INSERT INTO documents (user_id, project_title, contractor, date_ntp, proprietress)
            VALUES (%s, %s, %s, %s, %s) RETURNING id
        """, (current_user.id, project_title, contractor, date_ntp, proprietress))

        document_id = cur.fetchone()['id']
        conn.commit()

        # Define user and document folder structure
        user_folder = os.path.join("uploads", f"user_{current_user.id}")
        document_folder = os.path.join(user_folder, f"document_{document_id}")
        os.makedirs(document_folder, exist_ok=True)  # Ensure folder exists

        # Insert each file into the database and save to disk
        for file in files:
            if file.filename:
                filename = secure_filename(file.filename)
                file_path = os.path.join(document_folder, filename)
                file.save(file_path)

                cur.execute("""
                    INSERT INTO files (user_id, document_id, filename, folder_name, file_path, uploaded_at)
                    VALUES (%s, %s, %s, %s, %s, NOW())
                """, (current_user.id, document_id, filename, project_title, file_path))

        conn.commit()
        cur.close()
        conn.close()

        flash("Document and files uploaded successfully!", "success")
        return redirect(url_for('dashboard'))
    

    
# Protected Dashboard


#fetches document details and displays them in a new page (document_details.php)
@app.route('/document/<int:document_id>')
@login_required
def view_document(document_id):
    conn = get_db_connection()
    cur = conn.cursor()

    # Fetch document details
    print("Fetching files for document_id:", document_id)  # Debugging step
    cur.execute("SELECT * FROM documents WHERE id = %s", (document_id,))
    document = cur.fetchone()

    # Fetch uploaded files for this document
    
    cur.execute("SELECT filename, file_path FROM files WHERE document_id = %s", (document_id,))
    files = cur.fetchall()

    print("Files found:", files)  # Debugging step

    cur.close()
    conn.close()

    if not document:
        flash("Document not found!", "danger")
        return redirect(url_for('dashboard'))

    return render_template("document_details.php", document=document, files=files)


#download
@app.route('/download/<path:file_path>')
@login_required
def download_file(file_path):
    return send_file(file_path, as_attachment=True)

#preview

UPLOAD_FOLDER = "uploads"  # Set your upload directory
app.config["UPLOAD_FOLDER"] = UPLOAD_FOLDER

# Ensure Tesseract is installed and set the path
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"
@app.route("/preview", methods=["POST"])
def preview_file():
    file_path = request.form.get("file_path")
    file_ext = file_path.split(".")[-1].lower()
    full_path = os.path.join(app.config["UPLOAD_FOLDER"], file_path)  # Ensure correct path

    file_url = url_for("download_file", file_path=file_path)
    keywords = ["Notice to Proceed", "Notice of Award", "Purchase Request", "Supplemental Project Procurement Plan", "Memorandum of Agreement"]
    keyword_locations = []

    POPPLER_PATH = r"C:\\Users\\Rina Mae\\Documents\\Roy\\Practicum\\Release-24.08.0-0\\poppler-24.08.0\\Library\\bin\\pdf"  # Update to your actual Poppler path
    images = convert_from_path(full_path, poppler_path=POPPLER_PATH)

    if file_ext == "pdf":
       
        for i, image in enumerate(images):
            text = pytesseract.image_to_string(image)
            for keyword in keywords:
                if keyword in text:
                    keyword_locations.append({"text": keyword, "page": i + 1})

        return jsonify({"file_type": "pdf", "file_url": file_url, "keywords": keyword_locations})

    elif file_ext in ["png", "jpg", "jpeg"]:
        image = Image.open(full_path)  # Open the image properly
        text = pytesseract.image_to_string(image)
        for keyword in keywords:
            if keyword in text:
                keyword_locations.append({"text": keyword, "page": 1})

        return jsonify({"file_type": "image", "file_url": file_url, "keywords": keyword_locations})

    return jsonify({"error": "Unsupported file type"}), 400

#delete Function

@app.route('/delete/<int:doc_id>', methods=['DELETE'])
def delete_route(doc_id):
    return rmv_document(doc_id)



# Logout
@app.route('/logout')
@login_required
def logout():
    logout_user()
    flash("You have been logged out.", "info")
    return redirect(url_for('login'))

if __name__ == "__main__":
    app.run(debug=True)

