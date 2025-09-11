<?php
declare(strict_types=1);

namespace Entity;

class OrderItems {
    public int $id;
    public int $user_id;
    public int $category_id;
    public int $quantity;

    public function __construct(
        int $id,
        int $user_id,
        int $category_id,
        int $quantity
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->category_id = $category_id;
        $this->quantity = $quantity;
    }

    /** Удобно отдавать массив для json_encode */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'quantity' => $this->quantity,
        ];
    }
}
