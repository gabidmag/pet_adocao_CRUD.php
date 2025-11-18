<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$pedido_id) {
        header('Location: listar.php');
        exit();
    }

    try {
        $stmt_delete = $pdo->prepare("DELETE FROM adocoes WHERE id = :id");
        $stmt_delete->bindParam(':id', $pedido_id);
        $stmt_delete->execute();

        $_SESSION['mensagem_sucesso'] = "Pedido de adoção deletado com sucesso!"; 

    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao deletar pedido: " . $e->getMessage();
    }

    header('Location: listar.php');
    exit();
?>