<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$pedido_id) {
        header('Location: listar.php');
        exit();
    }

    // Deletar o pedido
    $stmt_delete = $mysqli->prepare("DELETE FROM adocoes WHERE id = ?");
    
    if ($stmt_delete) {
        $stmt_delete->bind_param("i", $pedido_id);

        if ($stmt_delete->execute()) {
            $_SESSION['mensagem_sucesso'] = "Pedido de adoção deletado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao deletar pedido: " . $stmt_delete->error;
        }

        $stmt_delete->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar exclusão: " . $mysqli->error;
    }

    header('Location: listar.php');
    exit();
?>
