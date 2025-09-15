<?php
declare(strict_types=1);

namespace Repository;

use PDO;
use Entity\OrderItems;
use Database;

class OrderItemsRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
    }

    private function mapRow(array $r): OrderItems
    {
        return new OrderItems(
            (int)$r['id'],
            (int)$r['order_id'],
            (int)$r['product_id'],
            (float)$r['price'],
            (int)$r['qty']
        );
    }

    public function create(int $orderId, OrderItems $item): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO order_items(order_id, product_id, price, qty) VALUES(:oid,:pid,:price,:qty)'
        );
        $st->execute([
            ':oid' => $orderId,
            ':pid' => $item->product_id,
            ':price' => $item->price,
            ':qty' => $item->qty,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /** @return OrderItems[] */
    public function listByOrder(int $orderId): array
    {
        $st = $this->pdo->prepare('SELECT * FROM order_items WHERE order_id = :oid');
        $st->execute([':oid' => $orderId]);
        return array_map(fn($r) => $this->mapRow($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function updateQty(int $itemId, int $qty): void
    {
        $st = $this->pdo->prepare('UPDATE order_items SET qty = :q WHERE id = :id');
        $st->execute([':id' => $itemId, ':q' => $qty]);
    }

    public function delete(int $itemId): void
    {
        $st = $this->pdo->prepare('DELETE FROM order_items WHERE id = :id');
        $st->execute([':id' => $itemId]);
    }
}
