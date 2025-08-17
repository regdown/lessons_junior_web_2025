<?php
$db = new PDO("sqlite:../database.db");

// Создание таблицы
$db->exec("CREATE TABLE IF NOT EXISTS Users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    age INTEGER
)");

// Добавление тестовых данных
$db->exec("INSERT INTO Users (name, email, age) VALUES ('Charlie', 'charlie@example.com', 22)");
$db->exec("INSERT INTO Users (name, email, age) VALUES ('Diana', 'diana@example.com', 28)");

echo "Миграция завершена ✅\n";
