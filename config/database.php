<?php

$host = "localhost";
$dbname = "twisted_threads_inventory";
$username = "root";
$password = "abcd4321";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>