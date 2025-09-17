<?php
$db = new PDO("sqlite:../database.db");

// Создание таблицы
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(255),
    surname VARCHAR(255),
    age INTEGER,
    email TEXT,
    adress TEXT,
    password TEXT
)");

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


$db->exec("CREATE TABLE IF NOT EXISTS items(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id), 
    FOREIGN KEY (product_id) REFERENCES products(id)
)");

//Добавление пользователей
$db->exec("INSERT INTO users (username, surname, age, email, adress, password) VALUES ('Поп', 'Питонов', 29, 'pop@mail.ru', 'Москва', '" . md5('PASS1') . "')");
$db->exec("INSERT INTO users (username,surname,age,email,adress,password) VALUES ('Джон','Уотсон',32,'rocky@mail.ru','Новосибирск', '" .md5('PASS2') . "')");
$db->exec("INSERT INTO users (username,surname,age,email,adress,password) VALUES ('Старшина','Адмиралтейский',66,'zvezda@mail.ru','Красная Площадь', '" .md5('PASS3') . "')");


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

//Добавление в корзину
$db->exec("INSERT INTO items (user_id,product_id,quantity) VALUES (3,3,1)");
$db->exec("INSERT INTO items (user_id,product_id,quantity) VALUES (1,3,1)");
$db->exec("INSERT INTO items (user_id,product_id,quantity) VALUES (3,2,1)");

echo "Миграция завершена ✅\n";

$user_id = 3; // ID пользователя, чью корзину нужно получить

$sql = "
    SELECT
        i.quantity,
        p.name AS product_name,
        p.price AS product_price
    FROM
        items i
    JOIN
        products p ON i.product_id = p.id
    WHERE
        i.user_id = :user_id
";

try {
    $stmt = $db->prepare($sql); // Подготовка запроса
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Привязка параметра
    $stmt->execute(); // Выполнение запроса
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC); // Получение результатов

    // 4. Вывод результатов
    if (count($cartItems) > 0) {
        echo "<h2>Корзина пользователя $user_id:</h2>";
        foreach ($cartItems as $item) {
            echo "Товар: " . $item['product_name'] . "<br>";
            echo "Цена: " . $item['product_price'] . "<br>";
            echo "Количество: " . $item['quantity'] . "<br>";
            echo "<hr>";
        }
    } else {
        echo "Корзина пользователя $user_id пуста.";
    }
} catch (PDOException $e) {
    echo "Ошибка при выполнении запроса: " . $e->getMessage();
}