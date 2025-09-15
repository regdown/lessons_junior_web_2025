<?php
declare(strict_types=1);

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Http/JsonResponse.php';

require_once __DIR__ . '/../classes/Entity/Address.php';
require_once __DIR__ . '/../classes/Repository/AddressRepository.php';

use Http\JsonResponse;
use Repository\AddressRepository;
use Entity\Address;

$dbFile = __DIR__ . '/../../database.db';

try {
    $db   = new Database($dbFile);
    $pdo  = $db->pdo();
    $repo = new AddressRepository($pdo);

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $st = $pdo->prepare('SELECT * FROM addresses WHERE id = :id');
            $st->execute([':id' => $id]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                JsonResponse::notFound(['message' => 'Address not found']);
                exit;
            }
            $addr = new Address(
                (int)$row['id'],
                (int)$row['user_id'],
                (string)$row['address'],
                (bool)$row['is_default'],
                (string)$row['created_at']
            );
            JsonResponse::ok($addr->toArray());
            exit;
        }

        if (!isset($_GET['user_id'])) {
            JsonResponse::badRequest(['message' => 'user_id is required']);
            exit;
        }
        $userId = (int)$_GET['user_id'];
        $items = array_map(function ($a) { return $a->toArray(); }, $repo->listByUser($userId));
        JsonResponse::ok($items);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ? $raw : '[]', true);

    if ($method === 'POST') {
        $required = ['user_id','address'];
        foreach ($required as $req) {
            if (!isset($payload[$req])) {
                JsonResponse::badRequest(['message' => "Field `$req` is required"]);
                exit;
            }
        }
        $addr = new Address(
            0,
            (int)$payload['user_id'],
            (string)$payload['address'],
            (bool)(isset($payload['is_default']) ? $payload['is_default'] : false),
            date('c')
        );
        $id = $repo->create($addr);

        $st = $pdo->prepare('SELECT * FROM addresses WHERE id = :id');
        $st->execute([':id' => $id]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        $addr = new Address(
            (int)$row['id'],
            (int)$row['user_id'],
            (string)$row['address'],
            (bool)$row['is_default'],
            (string)$row['created_at']
        );
        JsonResponse::created($addr->toArray());
        exit;
    }

    if ($method === 'PATCH') {
        if (!isset($_GET['id'])) {
            JsonResponse::badRequest(['message' => 'id is required']);
            exit;
        }
        $id = (int)$_GET['id'];

        $st = $pdo->prepare('SELECT * FROM addresses WHERE id = :id');
        $st->execute([':id' => $id]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            JsonResponse::notFound(['message' => 'Address not found']);
            exit;
        }
        $addr = new Address(
            $id,
            (int)$row['user_id'],
            isset($payload['address']) ? (string)$payload['address'] : (string)$row['address'],
            isset($payload['is_default']) ? (bool)$payload['is_default'] : (bool)$row['is_default'],
            (string)$row['created_at']
        );
        $repo->update($addr);

        $st->execute([':id' => $id]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        $addr = new Address(
            (int)$row['id'],
            (int)$row['user_id'],
            (string)$row['address'],
            (bool)$row['is_default'],
            (string)$row['created_at']
        );
        JsonResponse::ok($addr->toArray());
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

    JsonResponse::methodNotAllowed(['message' => 'Unsupported method']);
} catch (\Throwable $e) {
    JsonResponse::error(['message' => 'Server error: ' . $e->getMessage()], 500);
}
