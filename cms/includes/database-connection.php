<?php
$type = 'mysql';
$server = '127.0.0.1';
$db = "phpmysqlbook";
$port = '3004';
$charset = 'utf8mb4';

$username = 'root';
$password = 'duckett101';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];                                                      // Set PDO options

$dsn = "$type:host=$server;dbname=$db;port=$port;charset=$charset"; //Create DSN
try {                                                               // Try following code
    $pdo = new PDO($dsn, $username, $password, $options);            // Create PDO object
} catch (PDOException $e) {                                          // If exception thrown
    throw new PDOException($e->getMessage(), $e->getCode());        // Re-throw exception
}






