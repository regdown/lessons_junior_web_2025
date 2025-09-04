<?php
// общий bootstrap: подключение к БД и простые функции
declare(strict_types=1);

// путь к БД на уровень выше (подстройте под свой проект)
$dbPath = __DIR__ . '/../projects/catalog/database.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (Throwable $e) {
    http_response_code(500);
    exit('DB error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}

// helper: безопасный вывод
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
