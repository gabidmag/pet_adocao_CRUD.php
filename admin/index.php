<?php
    require_once '../verifica-login.php'; 
    require_once '../conexao.php'; 

    $nome_usuario = $_SESSION['usuario_nome'] ?? 'Administrador';
    $total_animais = 0;
    $animais_disponiveis = 0;

    try {
        $stmt_total = $pdo->query("SELECT COUNT(*) AS total FROM animais");
        $total_animais = $stmt_total->fetch()->total;
        
        $stmt_disponiveis = $pdo->query("SELECT COUNT(*) AS total FROM animais WHERE status = 'disponivel'");
        $animais_disponiveis = $stmt_disponiveis->fetch()->total;

    } catch (PDOException $e) {
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
            <a href="../logout.php">Sair</a>
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