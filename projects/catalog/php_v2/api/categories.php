<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';
require_once __DIR__ . '/../classes/Entity/Category.php';
require_once __DIR__ . '/../classes/Repository/CategoryRepository.php';

use Http\JsonResponse;
use Repository\CategoryRepository;

$dbFile = __DIR__ . '/../../product_catalog.db';

try {
    $db = new Database($dbFile);
    $repo = new CategoryRepository($db->pdo());
    $items = array_map(fn($c) => $c->toArray(), $repo->all());
    JsonResponse::ok($items);
} catch (\Throwable $e) {
    JsonResponse::error('Server error: ' . $e->getMessage(), 500);
}
