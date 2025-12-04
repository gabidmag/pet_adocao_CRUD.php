<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'conexao.php'; 

if (isset($mysqli)) {
    $conn = $mysqli;
} else {
    echo json_encode(['erro' => 'Falha na conexão']);
    exit;
}


$sql = "SELECT * FROM animais WHERE status = 'disponivel' ORDER BY id DESC";
$result = $conn->query($sql);

$lista = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        
        
        $nome_arquivo = basename($row['foto']);
        if (!empty($nome_arquivo)) {
            $row['imagem_final'] = 'public/uploads/' . $nome_arquivo;
        } else {
            $row['imagem_final'] = null;
        }
        
        $lista[] = $row;
    }
}


echo json_encode($lista, JSON_UNESCAPED_UNICODE);
?>