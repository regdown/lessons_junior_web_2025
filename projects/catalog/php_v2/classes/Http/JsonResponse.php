<?php
declare(strict_types=1);

namespace Http;

class JsonResponse
{
    public static function ok($data)
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function error($message, $status = 500)
    {
        http_response_code((int)$status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['message' => (string)$message], JSON_UNESCAPED_UNICODE);
    }
}
