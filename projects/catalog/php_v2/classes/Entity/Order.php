<?php
declare(strict_types=1);

namespace Entity;

class Order {
    public int $id;
    public int $user_id;
    public ?int $address_id;
    public string $payment_method; // 'cash'|'card'|'online'
    public string $order_status;   // 'new'|'processing'|'done'|'cancelled'
    public string $payment_status; // 'pending'|'paid'|'failed'|'refund'
    public float $total;
    public string $created_at;

    /** @var OrderItems[] */
    public array $items;

    /**
     * @param OrderItems[] $items
     */
    public function __construct(
        int $id,
        int $user_id,
        ?int $address_id,
        string $payment_method,
        string $order_status,
        string $payment_status,
        float $total,
        string $created_at,
        array $items = []
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->address_id = $address_id;
        $this->payment_method = $payment_method;
        $this->order_status = $order_status;
        $this->payment_status = $payment_status;
        $this->total = $total;
        $this->created_at = $created_at;
        $this->items = $items;
    }

    public function addItem(OrderItems $item): void {
        $this->items[] = $item;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'payment_method' => $this->payment_method,
            'order_status' => $this->order_status,
            'payment_status' => $this->payment_status,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'items' => array_map(static fn(OrderItems $i) => $i->toArray(), $this->items),
        ];
    }
}
