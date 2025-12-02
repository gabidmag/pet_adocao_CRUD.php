<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    // Pega o ID do pedido
    $pedido_id = $_GET['id'] ?? 0;

    if (!$pedido_id) {
        header('Location: listar.php');
        exit();
    }

    // Deletar o pedido
    $sql = "DELETE FROM adocoes WHERE id = $pedido_id";

    if (mysqli_query($mysqli, $sql)) {
        $_SESSION['mensagem_sucesso'] = "Pedido de adoção deletado com sucesso!";
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao deletar pedido: " . mysqli_error($mysqli);
    }

    header('Location: listar.php');
    exit();
?>
