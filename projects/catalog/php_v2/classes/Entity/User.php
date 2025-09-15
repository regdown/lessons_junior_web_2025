<?php
declare(strict_types=1);

namespace Entity;

class User {
    public int $id;
    public ?string $name;
    public ?string $surname;
    public ?string $patronymic;
    public ?int $age;
    public string $email;
    public ?string $gender;   // 'male'|'female'|'other'|null
    public ?string $phone;
    public bool $consent;     // 0/1 -> bool
    public string $created_at;

    public function __construct(
        int $id,
        ?string $name,
        ?string $surname,
        ?string $patronymic,
        ?int $age,
        string $email,
        ?string $gender,
        ?string $phone,
        bool $consent,
        string $created_at
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->age = $age;
        $this->email = $email;
        $this->gender = $gender;
        $this->phone = $phone;
        $this->consent = $consent;
        $this->created_at = $created_at;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'patronymic' => $this->patronymic,
            'age' => $this->age,
            'email' => $this->email,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'consent' => $this->consent,
            'created_at' => $this->created_at,
        ];
    }
}
