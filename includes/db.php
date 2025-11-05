<?php
$dsn = 'mysql:host=localhost;dbname=crud;charset=utf8mb4'; // <-- use crud
$user = 'root';
$pass = ''; // XAMPP default
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$pdo = new PDO($dsn, $user, $pass, $options);
