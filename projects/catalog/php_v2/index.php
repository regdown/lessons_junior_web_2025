<?php
// php/index.php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path = '/' . ltrim($path, '/');
$path = rtrim($path, '/');

switch ($path) {
    case '/api/products':
    case '/products':
        require __DIR__ . '/api/products.php';
        break;

    case '/api/categories':
    case '/categories':
        require __DIR__ . '/api/categories.php';
        break;

    case '/api/users':
    case '/users':
        require __DIR__ . '/api/users.php';
        break;

    case '/api/orders':
    case '/orders':
        require __DIR__ . '/api/orders.php';
        break;

    case '/api/addresses':
    case '/addresses':
        require __DIR__ . '/api/addresses.php';
        break;

    case '/api/order-items':
    case '/order-items':
        require __DIR__ . '/api/order-items.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(
            ['message' => 'Endpoint not found', 'path' => $path],
            JSON_UNESCAPED_UNICODE
        );
}
