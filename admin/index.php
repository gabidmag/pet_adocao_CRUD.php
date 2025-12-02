<?php
    require_once '../conexao.php'; 
    require_once '../verifica-login.php';
    require_login('../login.php');

    $nome_usuario = $_SESSION['nome_usuario'] ?? 'Administrador';
    $total_animais = 0;
    $animais_disponiveis = 0;

    // Total de animais
    $sql_total = "SELECT COUNT(*) AS total FROM animais";
    $resultado_total = mysqli_query($mysqli, $sql_total);
    if ($resultado_total) {
        $row = mysqli_fetch_assoc($resultado_total);
        $total_animais = $row['total'];
        mysqli_free_result($resultado_total);
    }

    // Animais disponíveis
    $sql_disponiveis = "SELECT COUNT(*) AS total FROM animais WHERE status = 'disponivel'";
    $resultado_disponiveis = mysqli_query($mysqli, $sql_disponiveis);
    if ($resultado_disponiveis) {
        $row = mysqli_fetch_assoc($resultado_disponiveis);
        $animais_disponiveis = $row['total'];
        mysqli_free_result($resultado_disponiveis);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Área Administrativa</title>
    <link rel="stylesheet" href="../public/css/style.css"> 
    <style>
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        .card h3 { margin-top: 0; color: #333; }
        .card a { display: block; margin-top: 10px; color: #007bff; text-decoration: none; font-weight: bold; }
        .card a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="index.php">Início</a> |
            <a href="animais/listar.php">Gerenciar Animais</a> |
            <a href="adocoes/listar.php">Gerenciar Adoções</a> |
            <?php if (is_logged_in()): ?>
                <a href="../logout.php">Sair</a>
            <?php else: ?>
                <a href="../login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>!</h2>
        <p>Utilize o menu acima ou os cartões abaixo para gerenciar o sistema de adoção de pets.</p>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Gerenciar Animais</h3>
                <p>Cadastre, edite e visualize os pets disponíveis.</p>
                <a href="animais/listar.php">Acessar Animais</a>
            </div>
            
            <div class="card">
                <h3>Gerenciar Adoções</h3>
                <p>Veja e gerencie os pedidos de adoção dos usuários.</p>
                <a href="adocoes/listar.php">Acessar Adoções</a>
            </div>
            
            <div class="card">
                <h3>Estatísticas</h3>
                <p>Total de Pets Cadastrados: **<?php echo $total_animais; ?>**</p>
                <p>Pets Disponíveis para Adoção: **<?php echo $animais_disponiveis; ?>**</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>