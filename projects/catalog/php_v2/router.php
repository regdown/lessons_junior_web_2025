<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$full = __DIR__ . $path;

// Если запрошен существующий файл/каталог — отдать как есть
if ($path !== '/' && file_exists($full)) {
    return false;
}

// Иначе — всё отдаём через index.php
require __DIR__ . '/index.php';