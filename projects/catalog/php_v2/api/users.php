<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';
require_once __DIR__ . '/../classes/Entity/User.php';
require_once __DIR__ . '/../classes/Repository/UserRepository.php';

use Http\JsonResponse;
use Repository\CategoryRepository;
use Repository\UserRepository;

$dbFile = __DIR__ . '/../../database.db';

try {
    $db = new Database($dbFile);
    $repo = new UserRepository($db->pdo());
    $items = array_map(fn($c) => $c->toArray(), $repo->all());
    JsonResponse::ok($items);
} catch (\Throwable $e) {
    JsonResponse::error('Server error: ' . $e->getMessage(), 500);
}
