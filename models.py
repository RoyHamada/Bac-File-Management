from app import db
from flask_login import UserMixin

# Users Table
class User(db.Model, UserMixin):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(100), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password = db.Column(db.String(255), nullable=False)

# Documents Table
class Document(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    project_title = db.Column(db.String(255), nullable=False)
    contractor = db.Column(db.String(255), nullable=False)
    date_ntp = db.Column(db.Date, nullable=False)
    proprietress = db.Column(db.String(255), nullable=False)

# Files Table
class File(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    filename = db.Column(db.String(255), nullable=False)
    filepath = db.Column(db.String(255), nullable=False)
    document_id = db.Column(db.Integer, db.ForeignKey('document.id'), nullable=False)
