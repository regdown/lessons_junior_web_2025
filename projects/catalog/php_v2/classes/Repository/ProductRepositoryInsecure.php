<?php
// php/classes/Repository/ProductRepositoryInsecure.php
declare(strict_types=1);

namespace Repository;

use PDO;

class ProductRepositoryInsecure {
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /** Возвращает все товары (безопасно) */
    public function all(): array {
        $stmt = $this->pdo->query('SELECT * FROM products ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * УМЫШЛЕННО УЯЗВИМО:
     * Строит SQL через конкатенацию — без параметров, без экранирования.
     * Любая строка в $categoryId будет вставлена в запрос "как есть".
     */
    public function byCategoryId($categoryId): array {
        // ❌ ПЛОХО: напрямую подставляем пользовательский ввод в SQL
        $sql = "SELECT * FROM products WHERE category_id = $categoryId ORDER BY id DESC";
        // Для наглядности можно логировать итоговый SQL:
        // error_log("[INSECURE SQL] " . $sql);

        $stmt = $this->pdo->query($sql); // тут произойдёт инъекция
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
