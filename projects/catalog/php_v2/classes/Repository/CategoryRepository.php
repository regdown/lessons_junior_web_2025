<?php
declare(strict_types=1);

namespace Repository;

use Entity\Category;
use PDO;

class CategoryRepository {
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function all(): array {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY name');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(
            fn($r) => new Category((int)$r['id'], (string)$r['name']),
            $rows
        );
    }
}
