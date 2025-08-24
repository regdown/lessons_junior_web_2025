<?php
declare(strict_types=1);

namespace Entity;

class Category {
    public int $id;
    public string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function toArray(): array {
        return ['id' => $this->id, 'name' => $this->name];
    }
}
