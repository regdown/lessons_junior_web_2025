# backend-python/migrations/001_create_products.py
import sqlite3

# подключаемся к файлу БД (если файла нет – создастся автоматически)
conn = sqlite3.connect("../../database.db")
cursor = conn.cursor()

# TODO: Сделать поле patronymic не обязательным
cursor.executescript("""
    DROP TABLE  users
""")

cursor.executescript("""
    CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            surname TEXT NOT NULL,
            patronymic TEXT NOT NULL,
            age INTEGER,
            email TEXT NOT NULL,
            adress TEXT
        );
""")


conn.commit()
conn.close()

print("Migration 004 applied successfully (SQLite).")
