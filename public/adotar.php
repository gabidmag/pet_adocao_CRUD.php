<?php
    require_once '../conexao.php';
    require_once '../verifica-login.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: animais.php');
        exit;
    }

    $animal_id = isset($_POST['animal_id']) ? (int) $_POST['animal_id'] : 0;
    $nome_adotante = trim($_POST['nome_adotante'] ?? '');
    $email_adotante = trim($_POST['email_adotante'] ?? '');
    $telefone_adotante = trim($_POST['telefone_adotante'] ?? '');
    $motivo_adocao = trim($_POST['motivo_adocao'] ?? '');

    $destino = 'animais.php';
    if ($animal_id > 0) {
        $destino = 'detalhe-animal.php?id=' . $animal_id;
    }

    $erros = [];

    if ($animal_id <= 0) {
        $erros[] = 'Animal inválido para adoção.';
    }

    if ($nome_adotante === '') {
        $erros[] = 'Informe seu nome completo.';
    }

    if ($email_adotante === '' || !filter_var($email_adotante, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um email válido.';
    }

    if ($telefone_adotante === '') {
        $erros[] = 'Informe um telefone para contato.';
    }

    if (!empty($erros)) {
        $_SESSION['mensagem_erro'] = implode(' ', $erros);
        header('Location: ' . $destino);
        exit;
    }

    $sql_animal = "SELECT nome, status FROM animais WHERE id = ? LIMIT 1";
    $stmt_animal = mysqli_prepare($mysqli, $sql_animal);

    if (!$stmt_animal) {
        $_SESSION['mensagem_erro'] = 'Erro ao preparar a busca do animal.';
        header('Location: ' . $destino);
        exit;
    }

    mysqli_stmt_bind_param($stmt_animal, 'i', $animal_id);
    mysqli_stmt_execute($stmt_animal);
    $resultado_animal = mysqli_stmt_get_result($stmt_animal);

    if (!$resultado_animal || mysqli_num_rows($resultado_animal) === 0) {
        $_SESSION['mensagem_erro'] = 'O animal informado não foi encontrado.';
        mysqli_stmt_close($stmt_animal);
        header('Location: ' . $destino);
        exit;
    }

    $dados_animal = mysqli_fetch_assoc($resultado_animal);
    mysqli_free_result($resultado_animal);
    mysqli_stmt_close($stmt_animal);

    if ($dados_animal['status'] !== 'disponivel') {
        $_SESSION['mensagem_erro'] = 'Este pet não está disponível para adoção.';
        header('Location: ' . $destino);
        exit;
    }

    if (!mysqli_begin_transaction($mysqli)) {
        $_SESSION['mensagem_erro'] = 'Não foi possível iniciar o processamento da adoção.';
        header('Location: ' . $destino);
        exit;
    }

    $sql_adocao = "INSERT INTO adocoes (animal_id, nome_adotante, email_adotante, telefone_adotante, motivo_adocao) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_adocao = mysqli_prepare($mysqli, $sql_adocao);

    if (!$stmt_adocao) {
        mysqli_rollback($mysqli);
        $_SESSION['mensagem_erro'] = 'Erro ao preparar o cadastro da adoção.';
        header('Location: ' . $destino);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt_adocao,
        'issss',
        $animal_id,
        $nome_adotante,
        $email_adotante,
        $telefone_adotante,
        $motivo_adocao
    );

    $inseriu = mysqli_stmt_execute($stmt_adocao);
    mysqli_stmt_close($stmt_adocao);

    if (!$inseriu) {
        mysqli_rollback($mysqli);
        $_SESSION['mensagem_erro'] = 'Não foi possível registrar o pedido de adoção.';
        header('Location: ' . $destino);
        exit;
    }

    $sql_update = "UPDATE animais SET status = 'adotado' WHERE id = ?";
    $stmt_update = mysqli_prepare($mysqli, $sql_update);

    if (!$stmt_update) {
        mysqli_rollback($mysqli);
        $_SESSION['mensagem_erro'] = 'Erro ao preparar a atualização do pet.';
        header('Location: ' . $destino);
        exit;
    }

    mysqli_stmt_bind_param($stmt_update, 'i', $animal_id);
    $atualizou = mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    if (!$atualizou) {
        mysqli_rollback($mysqli);
        $_SESSION['mensagem_erro'] = 'Não foi possível atualizar o status do pet.';
        header('Location: ' . $destino);
        exit;
    }

    mysqli_commit($mysqli);

    $_SESSION['mensagem_sucesso'] = 'Pedido de adoção enviado com sucesso! Entraremos em contato em breve.';
    header('Location: ' . $destino);
    exit;
?>