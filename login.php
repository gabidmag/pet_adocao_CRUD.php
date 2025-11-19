<?php
    require_once 'conexao.php';

    session_start();

    if (isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($password)) {
            include 'config/database.php';
            
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nome'] = $user['nome'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuário ou senha incorretos!';
            }
        } else {
            $error = 'Preencha todos os campos!';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Pet Adoção</title>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center;">Login Pet Adoção</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Usuário:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit">Entrar</button>
        </form>
        
        <div class="info">
            <strong>Credenciais para teste:</strong><br>
            Usuário: admin<br>
            Senha: password
        </div>
    </div>
</body>
</html>