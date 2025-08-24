# backend-python/migrations/001_create_products.py
import sqlite3

# подключаемся к файлу БД (если файла нет – создастся автоматически)
conn = sqlite3.connect("../product_catalog.db")
cursor = conn.cursor()

# создаём таблицу categories
cursor.execute("""
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);
""")

# создаём таблицу products
cursor.execute("""
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    image_url TEXT,
    category_id INTEGER,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
""")

# наполняем categories
cursor.executemany("INSERT INTO categories (name) VALUES (?)", [
    ("Electronics",), ("Books",)
])

# наполняем products начальными данными
cursor.executemany("""
INSERT INTO products (name, description, price, image_url, category_id)
VALUES (?, ?, ?, ?, ?)
""", [
    ("Smartphone XYZ", "Описание смартфона XYZ", 19999.99, None, 1),
    ("Book 'Learn Python'", "Учебник по Python для начинающих", 499.00, None, 2)
])

conn.commit()
conn.close()

print("Migration 001 applied successfully (SQLite).")
