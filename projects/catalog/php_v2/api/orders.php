<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';

require_once __DIR__ . '/../classes/Entity/Order.php';
require_once __DIR__ . '/../classes/Entity/OrderItems.php';

require_once __DIR__ . '/../classes/Repository/OrderRepository.php';
require_once __DIR__ . '/../classes/Repository/OrderItemsRepository.php';

use Http\JsonResponse;
use Repository\OrderRepository;
use Repository\OrderItemsRepository;
use Entity\Order;
use Entity\OrderItems;

$dbFile = __DIR__ . '/../../database.db';

try {
    $db   = new Database($dbFile);
    $pdo  = $db->pdo();
    $repo = new OrderRepository($pdo);
    $itemsRepo = new OrderItemsRepository($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $order = $repo->findById($id);
            if (!$order) {
                JsonResponse::notFound(['message' => 'Order not found']);
                exit;
            }
            JsonResponse::ok($order->toArray());
            exit;
        }

        if (!isset($_GET['user_id'])) {
            JsonResponse::badRequest(['message' => 'user_id is required']);
            exit;
        }
        $userId = (int)$_GET['user_id'];
        $orders = array_map(function ($o) { return $o->toArray(); }, $repo->listByUser($userId));
        JsonResponse::ok($orders);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ? $raw : '[]', true);

    if ($method === 'POST' && $action !== 'add_item') {
        $required = ['user_id','payment_method'];
        foreach ($required as $req) {
            if (!isset($payload[$req])) {
                JsonResponse::badRequest(['message' => "Field `$req` is required"]);
                exit;
            }
        }

        $items = [];
        if (!empty($payload['items']) && is_array($payload['items'])) {
            foreach ($payload['items'] as $it) {
                $items[] = new OrderItems(
                    0,
                    0,
                    (int)$it['product_id'],
                    (float)$it['price'],
                    (int)(isset($it['qty']) ? $it['qty'] : 1)
                );
            }
        }

        $order = new Order(
            0,
            (int)$payload['user_id'],
            isset($payload['address_id']) ? (int)$payload['address_id'] : null,
            (string)$payload['payment_method'],
            isset($payload['order_status']) ? (string)$payload['order_status'] : 'new',
            isset($payload['payment_status']) ? (string)$payload['payment_status'] : 'pending',
            isset($payload['total']) ? (float)$payload['total'] : 0.0,
            date('c'),
            $items
        );

        $orderId = $repo->create($order);
        $created = $repo->findById($orderId);
        JsonResponse::created($created ? $created->toArray() : null);
        exit;
    }

    if ($method === 'PATCH') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'id is required']);
            exit;
        }
        $id = (int)$_GET['id'];
        $repo->setStatuses(
            $id,
            isset($payload['order_status']) ? $payload['order_status'] : null,
            isset($payload['payment_status']) ? $payload['payment_status'] : null
        );
        $order = $repo->findById($id);
        JsonResponse::ok($order ? $order->toArray() : null);
        exit;
    }

    if ($method === 'DELETE') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'id is required']);
            exit;
        }
        $repo->delete((int)$_GET['id']);
        JsonResponse::noContent();
        exit;
    }

    // subaction: add_item
    if ($action === 'add_item' && $method === 'POST') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'order id is required']);
            exit;
        }
        $item = new OrderItems(
            0,
            (int)$_GET['id'],
            (int)$payload['product_id'],
            (float)$payload['price'],
            (int)(isset($payload['qty']) ? $payload['qty'] : 1)
        );
        $itemsRepo->create((int)$_GET['id'], $item);
        $repo->recalcTotal((int)$_GET['id']);
        $order = $repo->findById((int)$_GET['id']);
        JsonResponse::ok($order ? $order->toArray() : null);
        exit;
    }

    JsonResponse::methodNotAllowed(['message' => 'Unsupported method']);
} catch (\Throwable $e) {
    JsonResponse::error(['message' => 'Server error: ' . $e->getMessage()], 500);
}
