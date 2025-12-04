<?php
require_once 'conexao.php';
session_start();

// redireciona se já logado
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        
        $sql_check = "SELECT id_usuario FROM usuarios WHERE email_usuario = ? LIMIT 1";
        $stmt = mysqli_prepare($mysqli, $sql_check);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $erro = 'Email já cadastrado.';
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nome_usuario, email_usuario, senha_hash) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $hash);

            if (mysqli_stmt_execute($stmt)) {
                $sucesso = 'Cadastro realizado com sucesso. Você pode agora fazer login.';
            } else {
                $erro = 'Erro ao cadastrar usuário: ' . mysqli_error($mysqli);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta - PetAdopt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css"> <!-- Conecta ao seu CSS -->
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon"><i class="fa-solid fa-user-plus"></i></div>
            <h2>Crie sua Conta</h2>
            <p>Rápido e fácil para começar a favoritar pets!</p>
        </div>

        <!-- Mensagens de Sucesso ou Erro -->
        <?php if (!empty($sucesso)): ?>
            <div class="alert-error" style="background-color: #D1FAE5; color: #065F46;"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php elseif (!empty($erro)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <!-- Formulário Moderno -->
        <form method="POST" action="registrar_public.php" class="floating-label-form">
            <div class="input-group">
                <input type="text" id="nome" name="nome" placeholder=" " required>
                <label for="nome"><i class="fa-solid fa-user"></i> Nome Completo</label>
            </div>
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
            </div>
            <div class="input-group">
                <input type="password" id="senha" name="senha" placeholder=" " required>
                <label for="senha"><i class="fa-solid fa-lock"></i> Senha</label>
            </div>
            <button type="submit" class="btn-login">Criar Conta</button>
        </form>

        <div class="login-footer">
            <p>Já tem uma conta? <a href="login_public.php">Faça Login</a></p>
        </div>
    </div>

</body>
</html>