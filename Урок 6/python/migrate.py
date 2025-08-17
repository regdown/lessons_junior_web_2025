import sqlite3

# Подключаемся к SQLite (файл database.db создастся автоматически)
conn = sqlite3.connect("../database.db")
cursor = conn.cursor()

# Создаём таблицу Users
cursor.execute("""
CREATE TABLE IF NOT EXISTS Users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    age INTEGER
)
""")

# Добавляем тестовые данные
cursor.execute("INSERT INTO Users (name, email, age) VALUES (?, ?, ?)", ("Alice", "alice@example.com", 25))
cursor.execute("INSERT INTO Users (name, email, age) VALUES (?, ?, ?)", ("Bob", "bob@example.com", 30))

conn.commit()
conn.close()
print("Миграция завершена ✅")
