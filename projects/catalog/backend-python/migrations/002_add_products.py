# backend-python/migrations/001_create_products.py
import sqlite3

# подключаемся к файлу БД (если файла нет – создастся автоматически)
conn = sqlite3.connect('../../database.db')
cursor = conn.cursor()

# наполняем categories
cursor.executemany("INSERT INTO categories (id, name) VALUES (?, ?)", [
    (4, "Косметика"), (5, "Машины")
])

# наполняем products начальными данными
cursor.executemany("""
INSERT INTO products (name, description, price, category_id)
VALUES (?, ?, ?, ?)
""", [
    ("Тойота королла 2", "1987 года выпуска", 300000,  5),
    ("Шампунь", "Для седой бороды", 150,  4)
])

conn.commit()
conn.close()

print("Migration 001 applied successfully (SQLite).")
