# backend-python/migrations/001_create_products.py
import sqlite3

# подключаемся к файлу БД (если файла нет – создастся автоматически)
conn = sqlite3.connect("../../database.db")
cursor = conn.cursor()

# TODO: Сделать поле patronymic не обязательным
cursor.executescript("""
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        surname TEXT,
        patronymic TEXT,
        age INTEGER,
        email TEXT,
        adress TEXT
    );
""")

conn.commit()
conn.close()

print("Migration 001 applied successfully (SQLite).")
