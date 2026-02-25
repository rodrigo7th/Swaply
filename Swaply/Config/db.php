<?php

$host = "localhost";
$port = "5432";
$dbname = "Swaply";
$user = "postgres";
$password = "swaply2026";

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>
