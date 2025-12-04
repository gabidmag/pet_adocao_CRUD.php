<?php
// login_public.php
require_once 'conexao.php';
session_start();


if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin') {
    session_unset();
    session_destroy();
    session_start(); 
}

elseif (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'usuario') {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        $sql = "SELECT id_usuario, nome_usuario, senha_hash, ativo FROM usuarios WHERE email_usuario = ? LIMIT 1";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (!$user['ativo']) {
                $erro = 'Conta inativa.';
            } elseif (password_verify($senha, $user['senha_hash'])) {
                // Login bem-sucedido!
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nome_usuario'] = $user['nome_usuario'];
                
                $_SESSION['tipo_usuario'] = 'usuario'; 
                
                
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

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetAdopt</title>
    <!-- Ícones e CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fa-solid fa-user-circle"></i>
            </div>
            <h2>Acesse sua Conta</h2>
            <p>Faça login para favoritar pets e pedir adoções.</p>
        </div>

        <!-- Exibe mensagem de erro, se houver -->
        <?php if (!empty($erro)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login Público -->
        <form method="POST" action="login_public.php" class="floating-label-form">
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
            </div>
            <div class="input-group">
                <input type="password" id="senha" name="senha" placeholder=" " required>
                <label for="senha"><i class="fa-solid fa-lock"></i> Senha</label>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="login-footer">
          <p>Não tem uma conta? <a href="registrar_public.php">Cadastre-se</a></p>
        </div>

</body>
</html>