<?php

declare(strict_types=1);

namespace Entity;

class Address
{
    public int $id;
    public int $user_id;
    public string $address;
    public bool $is_default;
    public string $created_at;

    public function __construct(
        int    $id,
        int    $user_id,
        string $address,
        bool   $is_default,
        string $created_at
    )
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->address = $address;
        $this->is_default = $is_default;
        $this->created_at = $created_at;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address' => $this->address,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
        ];
    }
}
