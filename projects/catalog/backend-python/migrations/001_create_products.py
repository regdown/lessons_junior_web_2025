import sqlite3

conn = sqlite3.connect('../../database.db')
cursor = conn.cursor()


cursor.executescript("""
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT
    );

    CREATE TABLE IF NOT EXISTS brands (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT
    );

    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        description TEXT,
        image_url TEXT,
        category_id INTEGER,
        brand_id INTEGER,
        price REAL,  
        stock INTEGER,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        FOREIGN KEY (brand_id) REFERENCES brands(id)
    );
""")




# Добавляем категории
cursor.execute("INSERT INTO categories (name) VALUES ('Одежда')"),
cursor.execute("INSERT INTO categories (name) VALUES ('Обувь')"),
cursor.execute("INSERT INTO categories (name) VALUES ('Аксессуары')"),

# Добавляем бренды
cursor.execute("INSERT INTO brands (name) VALUES ('Nike')"),
cursor.execute("INSERT INTO brands (name) VALUES ('Adidas')"),
cursor.execute("INSERT INTO brands (name) VALUES ('RayBan')"),

# Добавляем товары
cursor.execute("""INSERT INTO products (name, description, price, stock, category_id, brand_id) VALUES ('Футболка хлопковая Оверсайз', 'Самая модная футболка гадом буду', 1499.00, 3,1,1)"""),
cursor.execute("""INSERT INTO products (name, description, price, stock, category_id, brand_id) VALUES ('Футбольные бутсы','Магаа, это лучшие бутсы, гадом буду. Месси на кампноу в них играл',11990,6,2,2)"""),
cursor.execute("""INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ('Очки солнцезащитные','Просто. Красиво. Удобно.',21299,17,3,3)"""),

conn.commit()
conn.close()

print("Миграция успешно завершена")