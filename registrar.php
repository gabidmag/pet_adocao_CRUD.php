<?php
require_once 'conexao.php';
session_start();

// redireciona se já logado
if (isset($_SESSION['id_usuario'])) {
    header('Location: admin/index.php');
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
        // evita duplicados
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Adoção</title>
</head>
<body>
    <div class="login-container">
        <h1>🐾 Sistema de Adoção</h1>
        <?php if ($erro): ?><div style="color:red;"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div style="color:green;"><?php echo htmlspecialchars($sucesso); ?></div><?php endif; ?>
        <p class="subtitle">Faça login para continuar</p>
            
        <form method="POST" action="registrar.php">
            <div class="form-group">
                <label for="nome_usuario">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            
            <div class="form-group">
                <label for="email_usuario">Email:</label>
                <input type="email_" id="email" name="email" required>
            </div>
                
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
                
            <button type="submit">Entrar</button>
        </form>
            
        <p>Já tem conta? <a href="login.php">Fazer login</a></p>
    </div>
</body>
</html>