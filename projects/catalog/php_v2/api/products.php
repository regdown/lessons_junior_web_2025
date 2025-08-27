<?php
declare(strict_types=1);

// === Подключаем классы ===
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';
require_once __DIR__ . '/../classes/Entity/Product.php';
require_once __DIR__ . '/../classes/Repository/ProductRepository.php';

use Http\JsonResponse;
use Repository\ProductRepository;

// путь к БД-файлу (лежит на уровень выше php/)
$dbFile = __DIR__ . '/../../database.db';

// Создаём соединение
$db = new Database($dbFile);
$repo = new ProductRepository($db->pdo());

// Параметр фильтрации по категории (опционально)
$categoryId = null;
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
    $categoryId = (int)$_GET['category_id'];
}

try {
    if ($categoryId !== null) {
        $items = array_map(fn($p) => $p->toArray(), $repo->byCategoryId($categoryId));
    } else {
        $items = array_map(fn($p) => $p->toArray(), $repo->all());
    }
    JsonResponse::ok($items);
} catch (\Throwable $e) {
    JsonResponse::error('Server error: ' . $e->getMessage(), 500);
}
