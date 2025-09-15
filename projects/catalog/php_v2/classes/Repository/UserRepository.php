<?php
declare(strict_types=1);

namespace Repository;

use PDO;
use Entity\User;
use Database;

class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        // Вставь сюда свой способ получения PDO, если отличается
        $this->pdo = $pdo ?? Database::getConnection();
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
    }

    private function mapRow(array $r): User
    {
        return new User(
            (int)$r['id'],
            $r['name'] ?? null,
            $r['surname'] ?? null,
            $r['patronymic'] ?? null,
            isset($r['age']) ? (int)$r['age'] : null,
            (string)$r['email'],
            $r['gender'] ?? null,
            $r['phone'] ?? null,
            (bool)$r['consent'],
            (string)$r['created_at']
        );
    }

    public function findById(int $id): ?User
    {
        $st = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $st = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $st->execute([':email' => $email]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    /** @return User[] */
    public function all(): array
    {
        $rows = $this->pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    public function create(User $u): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO users(name, surname, patronymic, age, email, gender, phone, consent)
             VALUES (:name,:surname,:patronymic,:age,:email,:gender,:phone,:consent)'
        );
        $st->execute([
            ':name' => $u->name,
            ':surname' => $u->surname,
            ':patronymic' => $u->patronymic,
            ':age' => $u->age,
            ':email' => $u->email,
            ':gender' => $u->gender,
            ':phone' => $u->phone,
            ':consent' => (int)$u->consent,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(User $u): void
    {
        $st = $this->pdo->prepare(
            'UPDATE users SET
                name = :name,
                surname = :surname,
                patronymic = :patronymic,
                age = :age,
                email = :email,
                gender = :gender,
                phone = :phone,
                consent = :consent
             WHERE id = :id'
        );
        $st->execute([
            ':id' => $u->id,
            ':name' => $u->name,
            ':surname' => $u->surname,
            ':patronymic' => $u->patronymic,
            ':age' => $u->age,
            ':email' => $u->email,
            ':gender' => $u->gender,
            ':phone' => $u->phone,
            ':consent' => (int)$u->consent,
        ]);
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $st->execute([':id' => $id]);
    }
}
