import os
import psycopg2
from flask import jsonify
from db_connect import get_db_connection  # Fix circular import!

def rmv_document(doc_id):
    try:
        conn = get_db_connection()
        cur = conn.cursor()

        # Fetch file paths before deleting files
        cur.execute("SELECT file_path FROM files WHERE document_id = %s", (doc_id,))
        files = cur.fetchall()

        # Delete files from the filesystem
        for file in files:
            file_path = file['file_path']
            if os.path.exists(file_path):
                os.remove(file_path)

        # Delete records from files table
        cur.execute("DELETE FROM files WHERE document_id = %s", (doc_id,))

        # Delete record from documents table
        cur.execute("DELETE FROM documents WHERE id = %s", (doc_id,))

        conn.commit()
        cur.close()
        conn.close()

        return jsonify({"message": f"Document {doc_id} and its files deleted successfully"}), 200

    except Exception as e:
        return jsonify({"error": str(e)}), 500
