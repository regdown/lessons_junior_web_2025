<?php
header("Content-Type: application/json; charset=UTF-8");

// Получаем чистый путь
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$path = '/' . ltrim($path, '/');     // нормализуем
$path = rtrim($path, '/');           // убираем завершающий слэш, кроме корня

switch ($path) {
    // поддерживаем оба варианта, вдруг сервер «съест» /api
    case '/api/products':
    //case '/products':
        require __DIR__ . '/api/products.php';
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "message" => "Endpoint not found",
            "path"    => $path
        ], JSON_UNESCAPED_UNICODE);
}



/**
 * switch ($request) {
 * case '/api/users':
 * require __DIR__ . '/api/users.php';
 * break;
 * default:
 * http_response_code(404);
 * echo json_encode(["message" => "Endpoint not found"]);
 * break;
 * }
 *
 */