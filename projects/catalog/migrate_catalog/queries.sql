CREATE TABLE categories(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255)
);

CREATE TABLE brands(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255)   
);

CREATE TABLE products(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255),
    description TEXT,
    category_id INTEGER,
    brand_id INTEGER,
    price DECIMAL(10,2),
    stock INTEGER,
    FOREIGN KEY (category_id) REFERENCES categories (id),
    FOREIGN KEY (brand_id) REFERENCES brands (id)
);


--Добавление категорий
INSERT INTO categories (name) VALUES ("Одежда");
INSERT INTO categories (name) VALUES ("Обувь");
INSERT INTO categories (name) VALUES ("Аксессуары");

--Добавление брендов
INSERT INTO brands (name) VALUES ("Nike");
INSERT INTO brands (name) VALUES ("Adidas");
INSERT INTO brands (name) VALUES ("RayBan");

--Добавление товаров
INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ("Футболка хлопковая Оверсайз","Самая модная футболка гадом буду",1499,3,1,1);
INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ("Футбольные бутсы","Магаа, это лучшие бутсы, гадом буду. Месси на кампноу в них играл",11990,6,2,2);
INSERT INTO products (name,description,price,stock,category_id,brand_id) VALUES ("Очки солнцезащитные","Просто. Красиво. Удобно.",21299,17,3,3);

--Показывать товар по категории
SELECT name FROM products WHERE category_id=1;

--Показывать товар по бренду
SELECT name FROM products WHERE brand_id=3;


