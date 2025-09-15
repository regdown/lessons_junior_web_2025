<?php
declare(strict_types=1);

namespace Entity;

class OrderItems {
    public int $id;
    public int $order_id;
    public int $product_id;
    public float $price;
    public int $qty;

    public function __construct(
        int $id,
        int $order_id,
        int $product_id,
        float $price,
        int $qty
    ) {
        $this->id = $id;
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->price = $price;
        $this->qty = $qty;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'qty' => $this->qty,
        ];
    }
}
