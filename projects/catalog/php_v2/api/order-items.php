<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';

require_once __DIR__ . '/../classes/Entity/OrderItems.php';
require_once __DIR__ . '/../classes/Repository/OrderItemsRepository.php';
require_once __DIR__ . '/../classes/Repository/OrderRepository.php';

use Http\JsonResponse;
use Repository\OrderItemsRepository;
use Repository\OrderRepository;
use Entity\OrderItems;

$dbFile = __DIR__ . '/../../database.db';

try {
    $db   = new Database($dbFile);
    $pdo  = $db->pdo();
    $repo = new OrderItemsRepository($pdo);
    $orders = new OrderRepository($pdo);

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        if (!isset($_GET['order_id'])) {
            JsonResponse::badRequest(['message' => 'order_id is required']);
            exit;
        }
        $items = array_map(function ($i) { return $i->toArray(); }, $repo->listByOrder((int)$_GET['order_id']));
        JsonResponse::ok($items);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ? $raw : '[]', true);

    if ($method === 'POST') {
        if (!isset($_GET['order_id'])) {
            JsonResponse::badRequest(['message' => 'order_id is required']);
            exit;
        }
        $required = ['product_id','price'];
        foreach ($required as $f) {
            if (!isset($payload[$f])) {
                JsonResponse::badRequest(['message' => "Field `$f` is required"]);
                exit;
            }
        }
        $item = new OrderItems(
            0,
            (int)$_GET['order_id'],
            (int)$payload['product_id'],
            (float)$payload['price'],
            (int)(isset($payload['qty']) ? $payload['qty'] : 1)
        );
        $repo->create((int)$_GET['order_id'], $item);
        $orders->recalcTotal((int)$_GET['order_id']);
        JsonResponse::created($item->toArray());
        exit;
    }

    if ($method === 'PATCH') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'id is required']);
            exit;
        }
        if (!isset($payload['qty'])) {
            JsonResponse::badRequest(['message' => 'qty is required']);
            exit;
        }
        $repo->updateQty((int)$_GET['id'], (int)$payload['qty']);
        JsonResponse::noContent();
        exit;
    }

    if ($method === 'DELETE') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'id is required']);
            exit;
        }
        $repo->delete((int)$_GET['id']);
        if (isset($_GET['order_id'])) {
            $orders->recalcTotal((int)$_GET['order_id']);
        }
        JsonResponse::noContent();
        exit;
    }

    JsonResponse::methodNotAllowed(['message' => 'Unsupported method']);
} catch (\Throwable $e) {
    JsonResponse::error(['message' => 'Server error: ' . $e->getMessage()], 500);
}
