<?php
declare(strict_types=1);

namespace Repository;

use Entity\Product;
use PDO;

class ProductRepository {
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function all(): array {
        $stmt = $this->pdo->query('SELECT * FROM products ORDER BY id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'mapRow'], $rows);
    }

    public function byCategoryId(int $categoryId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE category_id = :cid ORDER BY id DESC');
        $stmt->execute([':cid' => $categoryId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map([$this, 'mapRow'], $rows);
    }

    private function mapRow(array $r): Product {
        return new Product(
            (int)$r['id'],
            (string)$r['name'],
            $r['description'] !== null ? (string)$r['description'] : null,
            (float)$r['price'],
            $r['image_url'] !== null ? (string)$r['image_url'] : null,
            $r['category_id'] !== null ? (int)$r['category_id'] : null
        );
    }
}
