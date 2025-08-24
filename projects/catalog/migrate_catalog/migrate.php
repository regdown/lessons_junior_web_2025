<?php
$db = new PDO("sqlite:../database.db");

// Создание таблицы
$db->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255)
)");

$db->exec("CREATE TABLE IF NOT EXISTS brands (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255)
)");

$db->exec("CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255),
    description TEXT,
    category_id INTEGER,
    brand_id INTEGER,
    price DECIMAL(10,2),
    stock INTEGER,
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (brand_id) REFERENCES brands (id)
)");


// Добавление категорий
$db->exec("INSERT INTO categories (name) VALUES ('Одежда')");
$db->exec("INSERT INTO categories (name) VALUES ('Обувь')");
$db->exec("INSERT INTO categories (name) VALUES ('Аксессуары')");

// Добавление брендов
$db->exec("INSERT INTO brands (name) VALUES ('Nike')");
$db->exec("INSERT INTO brands (name) VALUES ('Adidas')");
$db->exec("INSERT INTO brands (name) VALUES ('RayBan')");

//Добавление продуктов
$db->exec("INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ('Футболка хлопковая Оверсайз','Самая модная футболка гадом буду',1499,3,1,1)");
$db->exec("INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ('Футбольные бутсы','Магаа, это лучшие бутсы, гадом буду. Месси на кампноу в них играл',11990,6,2,2)");
$db->exec("INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ('Очки солнцезащитные','Просто. Красиво. Удобно.',21299,17,3,3)");



echo "Миграция завершена ✅\n";
