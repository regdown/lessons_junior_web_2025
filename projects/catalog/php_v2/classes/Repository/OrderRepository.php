<?php
declare(strict_types=1);

namespace Repository;

use PDO;
use PDOException;
use Entity\Order;
use Entity\OrderItems;
use Database;

class OrderRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
    }

    private function mapOrder(array $r, array $items = []): Order
    {
        return new Order(
            (int)$r['id'],
            (int)$r['user_id'],
            isset($r['address_id']) ? (int)$r['address_id'] : null,
            (string)$r['payment_method'],
            (string)$r['order_status'],
            (string)$r['payment_status'],
            (float)$r['total'],
            (string)$r['created_at'],
            $items
        );
    }

    private function mapItem(array $r): OrderItems
    {
        return new OrderItems(
            (int)$r['id'],
            (int)$r['order_id'],
            (int)$r['product_id'],
            (float)$r['price'],
            (int)$r['qty']
        );
    }

    /** @return OrderItems[] */
    public function getItems(int $orderId): array
    {
        $st = $this->pdo->prepare('SELECT * FROM order_items WHERE order_id = :oid');
        $st->execute([':oid' => $orderId]);
        return array_map(fn($r) => $this->mapItem($r), $st->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findById(int $id): ?Order
    {
        $st = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id');
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return $this->mapOrder($row, $this->getItems((int)$row['id']));
    }

    /** @return Order[] */
    public function listByUser(int $userId): array
    {
        $st = $this->pdo->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY id DESC');
        $st->execute([':uid' => $userId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapOrder($r, $this->getItems((int)$r['id']));
        }
        return $result;
    }

    /**
     * Создаёт заказ и его позиции в транзакции.
     * Если $order->total == 0, пересчитает сумму из позиций.
     * Возвращает ID заказа.
     *
     * @param Order $order (items допускается передать внутри)
     */
    public function create(Order $order): int
    {
        try {
            $this->pdo->beginTransaction();

            $total = $order->total;
            if ($total <= 0 && !empty($order->items)) {
                foreach ($order->items as $it) {
                    $total += $it->price * $it->qty;
                }
            }

            $st = $this->pdo->prepare(
                'INSERT INTO orders(user_id, address_id, payment_method, order_status, payment_status, total)
                 VALUES(:uid, :addr, :pm, :os, :ps, :total)'
            );
            $st->execute([
                ':uid' => $order->user_id,
                ':addr' => $order->address_id,
                ':pm' => $order->payment_method,
                ':os' => $order->order_status,
                ':ps' => $order->payment_status,
                ':total' => $total,
            ]);
            $orderId = (int)$this->pdo->lastInsertId();

            if (!empty($order->items)) {
                $sti = $this->pdo->prepare(
                    'INSERT INTO order_items(order_id, product_id, price, qty)
                     VALUES(:oid, :pid, :price, :qty)'
                );
                foreach ($order->items as $it) {
                    $sti->execute([
                        ':oid' => $orderId,
                        ':pid' => $it->product_id,
                        ':price' => $it->price,
                        ':qty' => $it->qty,
                    ]);
                }
            }

            $this->pdo->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function setStatuses(int $orderId, ?string $orderStatus = null, ?string $paymentStatus = null): void
    {
        $parts = [];
        $params = [':id' => $orderId];
        if ($orderStatus !== null) { $parts[] = 'order_status = :os'; $params[':os'] = $orderStatus; }
        if ($paymentStatus !== null){ $parts[] = 'payment_status = :ps'; $params[':ps'] = $paymentStatus; }
        if (!$parts) return;

        $sql = 'UPDATE orders SET '.implode(', ', $parts).' WHERE id = :id';
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
    }

    public function addItem(int $orderId, OrderItems $item): int
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

    public function recalcTotal(int $orderId): void
    {
        $st = $this->pdo->prepare('SELECT SUM(price * qty) AS total FROM order_items WHERE order_id = :oid');
        $st->execute([':oid' => $orderId]);
        $total = (float)($st->fetchColumn() ?: 0);
        $u = $this->pdo->prepare('UPDATE orders SET total = :t WHERE id = :id');
        $u->execute([':t' => $total, ':id' => $orderId]);
    }

    public function delete(int $orderId): void
    {
        $st = $this->pdo->prepare('DELETE FROM orders WHERE id = :id'); // каскадом удалит позиции
        $st->execute([':id' => $orderId]);
    }
}
