<?php
declare(strict_types=1);

namespace Repository;

use Entity\User;
use PDO;

class UserRepository {
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function all(): array {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(
            fn($r) => new User(
                (int)$r['id'],
                (string)$r['name'],
                (string)$r['surname'],
                (string)$r['patronymic'],
                (int)$r['age'],
                (string)$r['email'],
                (string)$r['adress'],),
            $rows
        );
    }
}
