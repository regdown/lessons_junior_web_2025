<?php
declare(strict_types=1);

namespace Entity;

class User{
    public int $id;
    public string $username;
    public string $surname;
    public int $age;
    public string $email;
    public string $adress;


public function __construct(
    int $id,
    string $username,
    string $surname,
    int $age,
    string $email,
    string $adress
    
) {
    $this->id=$id;
    $this->username=$username;
    $this->surname=$surname;
    $this->age=$age;
    $this->email=$email;
    $this->adress=$adress;

}
public function toArray(): array {
    return [
        'id' => $this->id,
        'username' => $this->username,
        'surname' => $this->surname,
        'age' => $this->age,
        'email' => $this->email,
        'adress' => $this->adress
    ];
}

}