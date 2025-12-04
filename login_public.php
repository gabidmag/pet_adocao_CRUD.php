<?php
require_once 'conexao.php';
session_start();

// se já está logado, vai para admin
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $sql = "SELECT id_usuario, nome_usuario, senha_hash, ativo FROM usuarios WHERE email_usuario = ? LIMIT 1";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (!$user['ativo']) {
                $erro = 'Conta inativa.';
            } elseif (password_verify($senha, $user['senha_hash'])) {
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nome_usuario'] = $user['nome_usuario'];
                header('Location: index.php');
                exit;
            } else {
                $erro = 'Email ou senha inválidos.';
            }
        } else {
            $erro = 'Email ou senha inválidos.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>