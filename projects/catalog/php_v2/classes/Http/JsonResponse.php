<?php
declare(strict_types=1);

namespace Http;

class JsonResponse
{
    private static function send($data, int $status = 200, array $headers = []): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        foreach ($headers as $h) {
            header($h);
        }
        http_response_code($status);

        // Если пришла строка/число/булево — оборачиваем в { "message": ... }
        if (is_array($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['message' => (string)$data], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    public static function ok($data): void
    {
        self::send($data, 200);
    }

    public static function created($data): void
    {
        self::send($data, 201);
    }

    public static function noContent(): void
    {
        http_response_code(204);
        // тело по стандарту можно не слать
        exit;
    }

    public static function error($data, int $status = 500): void
    {
        self::send($data, $status);
    }

    public static function badRequest($data): void
    {
        self::send($data, 400);
    }

    public static function notFound($data): void
    {
        self::send($data, 404);
    }

    public static function methodNotAllowed($data): void
    {
        self::send($data, 405, ['Allow: GET, POST, PATCH, DELETE']);
    }
}
