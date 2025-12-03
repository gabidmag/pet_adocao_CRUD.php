<?php
// api_pets.php
// 1. Desliga avisos chatos que quebram o JSON
error_reporting(0);
ini_set('display_errors', 0);

// 2. Define que o resultado é JSON
header('Content-Type: application/json; charset=utf-8');

// 3. Tenta conectar
$conexao_sucesso = false;

// Procura o arquivo de conexão na raiz ou na pasta admin
if (file_exists('conexao.php')) {
    require_once 'conexao.php';
} elseif (file_exists('admin/conexao.php')) {
    require_once 'admin/conexao.php';
}

// 4. Padroniza a variável de conexão ($conn)
if (isset($mysqli) && $mysqli) {
    $conn = $mysqli;
    $conexao_sucesso = true;
} elseif (isset($conexao) && $conexao) {
    $conn = $conexao;
    $conexao_sucesso = true;
} elseif (isset($conn) && $conn) {
    // já está certa
    $conexao_sucesso = true;
}

// Se não conectou, devolve erro limpo em JSON
if (!$conexao_sucesso) {
    echo json_encode(["erro" => "Não foi possível conectar ao banco de dados."]);
    exit;
}

// 5. Busca os dados
$sql = "SELECT * FROM animais WHERE status = 'disponivel' ORDER BY id DESC";
$result = $conn->query($sql);

$lista = [];

if ($result) {
    while($row = $result->fetch_assoc()) {
        
        // Corrige a imagem
        $foto_banco = $row['foto'];
        $nome_arquivo = basename($foto_banco); // Limpa o caminho, pega só "foto.jpg"
        
        if (!empty($nome_arquivo)) {
            // Caminho absoluto para evitar erros
            $row['imagem_final'] = 'public/uploads/' . $nome_arquivo;
        } else {
            $row['imagem_final'] = null;
        }
        
        // Garante que caracteres especiais (acentos) não quebrem
        $row['nome'] = mb_convert_encoding($row['nome'], 'UTF-8', 'ISO-8859-1');
        $row['descricao'] = mb_convert_encoding($row['descricao'], 'UTF-8', 'ISO-8859-1');
        // Se seu banco já for UTF-8, pode remover as duas linhas acima

        $lista[] = $row;
    }
}

// 6. Entrega o resultado
echo json_encode($lista);
?>