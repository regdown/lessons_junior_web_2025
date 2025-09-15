<?php
declare(strict_types=1);

namespace Repository;

use PDO;
use Entity\Address;
use Database;

class AddressRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
    }

    private function mapRow(array $r): Address
    {
        return new Address(
            (int)$r['id'],
            (int)$r['user_id'],
            (string)$r['address'],
            (bool)$r['is_default'],
            (string)$r['created_at']
        );
    }

    /** @return Address[] */
    public function listByUser(int $userId): array
    {
        $st = $this->pdo->prepare('SELECT * FROM addresses WHERE user_id = :uid ORDER BY id DESC');
        $st->execute([':uid' => $userId]);
        return array_map(fn($r) => $this->mapRow($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getDefaultByUser(int $userId): ?Address
    {
        $st = $this->pdo->prepare('SELECT * FROM addresses WHERE user_id = :uid AND is_default = 1 LIMIT 1');
        $st->execute([':uid' => $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function create(Address $a): int
    {
        if ($a->is_default) {
            // Снимаем «дефолт» с остальных адресов пользователя
            $st = $this->pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :uid');
            $st->execute([':uid' => $a->user_id]);
        }
        $st = $this->pdo->prepare(
            'INSERT INTO addresses(user_id, address, is_default) VALUES (:uid,:addr,:def)'
        );
        $st->execute([
            ':uid' => $a->user_id,
            ':addr' => $a->address,
            ':def' => (int)$a->is_default,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(Address $a): void
    {
        if ($a->is_default) {
            $st0 = $this->pdo->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :uid');
            $st0->execute([':uid' => $a->user_id]);
        }
        $st = $this->pdo->prepare(
            'UPDATE addresses SET address = :addr, is_default = :def WHERE id = :id'
        );
        $st->execute([
            ':id' => $a->id,
            ':addr' => $a->address,
            ':def' => (int)$a->is_default,
        ]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM addresses WHERE id = :id');
        $st->execute([':id' => $id]);
    }
}
