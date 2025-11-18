<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $animal_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$animal_id) {
        header('Location: listar.php');
        exit();
    }

    try {
        // 1. Busca o nome da foto antes de deletar o registro 
        $stmt_select = $pdo->prepare("SELECT foto FROM animais WHERE id = :id");
        $stmt_select->bindParam(':id', $animal_id);
        $stmt_select->execute();
        $animal = $stmt_select->fetch();
        
        // 2. Deleta o registro do banco de dados
        $stmt_delete = $pdo->prepare("DELETE FROM animais WHERE id = :id");
        $stmt_delete->bindParam(':id', $animal_id);
        $stmt_delete->execute();

        // 3. Deleta o arquivo da foto do servidor 
        if ($animal && $animal->foto) {
            $foto_caminho = '../../uploads/' . $animal->foto;
            if (file_exists($foto_caminho)) {
                unlink($foto_caminho);
            }
        }

        
        $_SESSION['mensagem_sucesso'] = "Animal deletado com sucesso!"; 

    } catch (PDOException $e) {
        
        $_SESSION['mensagem_erro'] = "Erro ao deletar animal: " . $e->getMessage();
    }

    
    header('Location: listar.php');
    exit();
?>