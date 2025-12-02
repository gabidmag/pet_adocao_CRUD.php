<?php
    // conexao.php - Configuração do banco de dados

    $host = 'localhost:3306';
    $usuario = 'root';
    $senha = '';
    $banco = 'adocao_animais';
    
    // Conecta ao banco
    $mysqli = mysqli_connect($host, $usuario, $senha, $banco);

    // Verifica se conectou
    if (!$mysqli) {
        echo "❌ Erro na Conexão: " . mysqli_connect_error();
        exit();
    }

    // Define o charset
    mysqli_set_charset($mysqli, "utf8");
?>