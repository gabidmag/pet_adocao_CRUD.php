<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $animal_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$animal_id) {
        header('Location: listar.php');
        exit();
    }

    // 1. Busca o nome da foto antes de deletar o registro 
    $animal = null;

    $stmt_select = $mysqli->prepare("SELECT foto FROM animais WHERE id = ?");
    if ($stmt_select) {
        $stmt_select->bind_param("i", $animal_id);

        if ($stmt_select->execute()) {
            $resultado = $stmt_select->get_result();
            $animal = $resultado->fetch_object();
            $resultado->free();
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao buscar animal: " . $stmt_select->error;
            $stmt_select->close();
            header('Location: listar.php');
            exit();
        }

        $stmt_select->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar consulta: " . $mysqli->error;
        header('Location: listar.php');
        exit();
    }

    // 2. Deleta o registro do banco de dados
    $stmt_delete = $mysqli->prepare("DELETE FROM animais WHERE id = ?");
    if ($stmt_delete) {
        $stmt_delete->bind_param("i", $animal_id);

        if ($stmt_delete->execute()) {

            // 3. Deleta o arquivo da foto do servidor 
            if ($animal && $animal->foto) {
                $foto_caminho = '../../uploads/' . $animal->foto;
                if (file_exists($foto_caminho)) {
                    unlink($foto_caminho);
                }
            }

            $_SESSION['mensagem_sucesso'] = "Animal deletado com sucesso!";

        } else {
            $_SESSION['mensagem_erro'] = "Erro ao deletar animal: " . $stmt_delete->error;
        }

        $stmt_delete->close();
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar exclusão: " . $mysqli->error;
    }

    header('Location: listar.php');
    exit();
?>
