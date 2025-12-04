<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $animal_id = $_GET['id'] ?? 0;

    if (!$animal_id) {
        header('Location: listar.php');
        exit();
    }

    
    $sql_select = "SELECT foto FROM animais WHERE id = $animal_id";
    $resultado = mysqli_query($mysqli, $sql_select);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $animal = mysqli_fetch_assoc($resultado);
        $foto_nome = $animal['foto'];
    } else {
        $_SESSION['mensagem_erro'] = "Animal não encontrado";
        header('Location: listar.php');
        exit();
    }

    
    $sql_delete = "DELETE FROM animais WHERE id = $animal_id";

    if (mysqli_query($mysqli, $sql_delete)) {
        
        
        if (!empty($foto_nome)) {
            $foto_caminho = '../../uploads/' . $foto_nome;
            if (file_exists($foto_caminho)) {
                unlink($foto_caminho);
            }
        }
        
        $_SESSION['mensagem_sucesso'] = "Animal deletado com sucesso!";
        
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao deletar: " . mysqli_error($mysqli);
    }

    header('Location: listar.php');
    exit();
?>
