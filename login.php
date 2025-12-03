<?php
require_once 'conexao.php';
session_start();

// se já está logado, vai para admin
if (isset($_SESSION['id_usuario'])) {
    header('Location: admin/index.php');
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
                header('Location: admin/index.php');
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetAdopt</title>
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Seu CSS Moderno -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fa-solid fa-user-circle"></i>
            </div>
            <h2>Bem-vindo de volta!</h2>
            <p>Entre para gerenciar as adoções.</p>
        </div>

        <!-- Exibe mensagem de erro se o PHP detectou algo -->
        <?php if (!empty($erro)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="floating-label-form">
    
          <!-- Campo de Email -->
        <div class="input-group">
        <input type="email" id="email" name="email" placeholder=" " required>
        <label for="email">
            <i class="fa-solid fa-envelope"></i> Email
        </label>
    </div>

    <!-- Campo de Senha -->
    <div class="input-group">
        <input type="password" id="senha" name="senha" placeholder=" " required>
        <label for="senha">
            <i class="fa-solid fa-lock"></i> Senha
        </label>
    </div>

    <button type="submit" class="btn-login">Entrar</button>
</form>

<div class="login-footer">
    <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Voltar ao Site</a>
    <span> | </span>
    <a href="registrar.php">Criar Conta</a>
</div>

</body>
</html>