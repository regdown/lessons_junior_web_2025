<?php
// backend-php/config/database.php

try {
    $conn = new PDO("sqlite:" . __DIR__ . "/../../product_catalog.db");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    die("Connection error: " . $exception->getMessage());
}
