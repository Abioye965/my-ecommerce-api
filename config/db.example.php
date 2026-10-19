<?php

$host = 'localhost';
$db = 'db-name';
$username = 'your-db-username';
$password = 'your-db-password';

$pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_FOUND_ROWS => true,
    ],
);
