<?php
declare(strict_types=1);

class Database {
    private \PDO $conn;

    public function __construct(string $dbFile)
    {
        $this->conn = new \PDO('sqlite:' . $dbFile);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->conn->exec('PRAGMA foreign_keys = ON;'); // на всякий случай
    }

    public function pdo(): \PDO
    {
        return $this->conn;
    }
}
