<?php
declare(strict_types=1);

namespace Entity;

class User{
    public int $id;
    public string $name;
    public string $surname;
    public ?string $patronymic;
    public int $age;
    public string $email;
    public string $adress;


public function __constract(
    int $id,
    string $name,
    string $surname,
    ?string $patronymic,
    int $age,
    string $email,
    string $adress
    
) {
    $this->id=$id;
    $this->name=$name;
    $this->surname=$surname;
    $this->patronymic=$patronymic;
    $this->age=$age;
    $this->email=$email;
    $this->adress=$adress;

}
public function toArray(): array {
    return [
        'id' => $this->id,
        'name' => $this->name,
        'surname' => $this->surname,
        'patronymic' => $this->patronymic,
        'age' => $this->age,
        'email' => $this->email,
        'adress' => $this->adress
    ];
}

}