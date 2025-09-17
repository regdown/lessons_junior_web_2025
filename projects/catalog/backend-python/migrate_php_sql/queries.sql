 CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(255),
        surname VARCHAR(255),
        age INTEGER,
        email TEXT,
        adress TEXT,
        password TEXT
 );

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
CREATE TABLE items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,        -- ID пользователя
    product_id INTEGER NOT NULL,     -- ID товара
    quantity INTEGER NOT NULL DEFAULT 1, --Количество товаров
    FOREIGN KEY (user_id) REFERENCES users(id), 
    FOREIGN KEY (product_id) REFERENCES products(id)
);




--Добавление пользователей
INSERT INTO users (username,surname,age,email,adress,password) VALUES ("Поп","Питонов",29,"pop@mail.ru","Москва",md5("PASS1"));
INSERT INTO users (username,surname,age,email,adress) VALUES ("Джон","Уотсон",32,"rocky@mail.ru","Новосибирск",md5("PASS2"));
INSERT INTO users (username,surname,age,email,adress) VALUES ("Старшина","Адмиралтейский",66,"zvezda@mail.ru",md5("Красная Площадь","PASS3"));

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

--Добавление в корзину
INSERT INTO items (user_id,product_id,quantity) VALUES (3,3,1);
INSERT INTO items (user_id,product_id,quantity) VALUES (1,1,1);
INSERT INTO items (user_id,product_id,quantity) VALUES (3,2,1);

SELECT
    i.id AS cart_item_id,        -- ID элемента корзины
    p.name AS product_name,       -- Название товара
    p.price AS product_price,      -- Цена товара
    p.description AS product_description, -- Описание товара
    p.stock AS product_stock,      -- Остаток товара
    c.name AS category_name,      -- Название категории
    b.name AS brand_name,         -- Название бренда
    i.quantity                   -- Количество товара в корзине
FROM
    items i                       -- Таблица корзины (псевдоним i)
JOIN
    products p ON i.product_id = p.id  -- Соединяем с таблицей товаров по product_id
JOIN
    categories c ON p.category_id = c.id  -- Соединяем с таблицей категорий по category_id
JOIN
    brands b ON p.brand_id = b.id     -- Соединяем с таблицей брендов по brand_id
WHERE
    i.user_id = 3;              



Илья Исаев, [10 сент. 2025 г., 18:43:49]:
select
    u.id,
    u.email,
    u.name,
    u.created_at,
    count(o.id) total_orders,
    sum(o.total_cents) total_cents,
    avg(o.total_cents)
from users u
LEFT JOIN orders o on o.user_id = u.id
GROUP BY
    u.id,
    u.email,
    u.name,
    u.created_at
order by sum(o.total_cents) desc


select
    u.id,
    u.email,
    u.name,
    u.created_at,
    count(o.id) total_orders,
    sum(o.total_cents) total_cents,
    avg(o.total_cents)
from users u
INNER JOIN orders o on o.user_id = u.id
WHERE u.id > 10000
GROUP BY
    u.id,
    u.email,
    u.name,
    u.created_at
HAVING count(o.id) >=12
order by sum(o.total_cents) desc