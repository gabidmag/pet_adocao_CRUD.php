<?php

$host = 'localhost';
$dbname = 'petadocao_db';
$user = 'root';
$pass = ''; // padrao vazio

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

?>