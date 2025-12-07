<?php

$host = "127.0.0.1";
$port = 3307;
$user = "phpuser";
$pass = "php123!";
$db   = "research_paper_archive";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

}
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
