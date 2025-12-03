<?php
    require_once '../conexao.php'; 
    require_once '../verifica-login.php';
    require_login('../login.php');

    $nome_usuario = $_SESSION['nome_usuario'] ?? 'Administrador';
    
    // Inicializa contadores com 0
    $total_animais = 0;
    $animais_disponiveis = 0;
    $pedidos_pendentes = 0;

    // 1. Conta Total de Animais
    if ($result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM animais")) {
        $total_animais = mysqli_fetch_assoc($result)['total'];
    }

    // 2. Conta Animais Disponíveis (Vitrine)
    if ($result = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM animais WHERE status = 'disponivel'")) {
        $animais_disponiveis = mysqli_fetch_assoc($result)['total'];
    }

    // 3. Conta Pedidos de Adoção Pendentes
    // O @ esconde erro caso a tabela adocoes ainda não tenha sido criada
    $sql_pedidos = "SELECT COUNT(*) as total FROM adocoes WHERE status = 'pendente'";
    if ($result = @mysqli_query($mysqli, $sql_pedidos)) {
        $pedidos_pendentes = mysqli_fetch_assoc($result)['total'];
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin PetAdopt</title>
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Seu CSS Novo -->
    <link rel="stylesheet" href="../public/css/style.css"> 
</head>
<body>

    <!-- Navbar Admin -->
    <nav class="navbar">
        <div class="logo"><i class="fa-solid fa-chart-line"></i> Dashboard</div>
        <div class="nav-actions">
            <span style="margin-right: 20px; font-weight: 500; color: var(--text-dark);">Olá, <?php echo htmlspecialchars($nome_usuario); ?></span>
            <a href="../index.php" style="margin-right: 15px; color: var(--primary-color);">Ver Site</a>
            <a href="../logout.php" class="btn-details" style="background-color: #EF4444; border:none;">Sair</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <!-- Banner de Boas Vindas -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h2>Bem-vindo ao Painel!</h2>
                <p>Aqui você tem uma visão geral de todos os pets e solicitações de adoção.</p>
            </div>
            <i class="fa-solid fa-shield-dog" style="font-size: 4rem; opacity: 0.3;"></i>
        </div>

        <!-- Grid de Cards Profissionais -->
        <div class="stats-grid">
            
            <!-- Card 1: Animais Totais -->
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-number"><?php echo $total_animais; ?></div>
                        <div class="stat-label">Total de Pets</div>
                    </div>
                    <div class="stat-icon icon-blue">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                </div>
                <div class="card-action">
                    <a href="animais/listar.php" class="btn-link link-blue">
                        Gerenciar Animais <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: Disponíveis -->
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-number"><?php echo $animais_disponiveis; ?></div>
                        <div class="stat-label">Na Vitrine</div>
                    </div>
                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-shop"></i>
                    </div>
                </div>
                <div class="card-action">
                    <a href="animais/criar.php" class="btn-link link-green">
                        Cadastrar Novo <i class="fa-solid fa-plus"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Pedidos Pendentes -->
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-number"><?php echo $pedidos_pendentes; ?></div>
                        <div class="stat-label">Pedidos Pendentes</div>
                    </div>
                    <div class="stat-icon icon-orange">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                </div>
                <div class="card-action">
                    <a href="adocoes/listar.php" class="btn-link link-orange">
                        Ver Solicitações <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Card 4: Sistema -->
            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-number" style="font-size:1.5rem; margin-top:10px;">Sistema</div>
                        <div class="stat-label">Status</div>
                    </div>
                    <div class="stat-icon icon-purple">
                        <i class="fa-solid fa-server"></i>
                    </div>
                </div>
                <div style="margin-top: 10px; color: #10B981; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-circle-check"></i> Online
                </div>
            </div>

        </div>
    </div>

</body>
</html>