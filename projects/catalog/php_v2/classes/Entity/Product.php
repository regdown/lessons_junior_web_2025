<?php
declare(strict_types=1);

namespace Entity;

class Product {
    public int $id;
    public string $name;
    public ?string $description;
    public float $price;
    public ?string $image_url;
    public ?int $category_id;

    public function __construct(
        int $id,
        string $name,
        ?string $description,
        float $price,
        ?string $image_url,
        ?int $category_id
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->image_url = $image_url;
        $this->category_id = $category_id;
    }

    /** Удобно отдавать массив для json_encode */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'category_id' => $this->category_id,
        ];
    }
}
