<?php
header("Content-Type: application/json");

$db = new PDO("sqlite:../database.db");
$stmt = $db->query("SELECT id, name, email, age FROM Users");

$users = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[] = $row;
}

echo json_encode($users, JSON_PRETTY_PRINT);
