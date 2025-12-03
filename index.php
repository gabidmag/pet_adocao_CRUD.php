<?php
// Mantemos apenas o necessário para saber se é admin ou visitante
require_once 'conexao.php';

// Tenta iniciar sessão para verificar se o admin está logado
if(!isset($_SESSION)) {
    session_start();
}

// Função simples para checar login (caso o verifica-login.php force redirecionamento, usamos essa verificação manual)
function esta_logado() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetAdopt - Encontre seu Melhor Amigo</title>
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Seu CSS Moderno -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fa-solid fa-heart"></i> PetAdopt
        </div>
        <ul class="nav-links">
            <li><a href="#pets-container">Nossos Pets</a></li>
            
            <?php if (esta_logado()): ?>
                <!-- Se for Admin, mostra botão para ir ao Painel -->
                <li><a href="admin/index.php" class="btn-details" style="background-color: #10B981;">Painel Admin</a></li>
                <li><a href="logout.php" style="color: #EF4444;"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
            <?php else: ?>
                <!-- Se for Visitante, mostra botão de Área Restrita -->
                <li><a href="login.php" class="btn-details">Área Restrita</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-actions">
            <button id="theme-toggle"><i class="fa-solid fa-sun"></i></button>
        </div>
    </nav>

    <!-- Hero Section (A parte bonita do topo) -->
    <header class="hero">
        <h1>Adote um <span class="highlight">Novo Amigo</span></h1>
        <p>Milhares de animais estão esperando por um lar cheio de amor...</p>
        
        <div class="search-bar-container">
            <input type="text" id="searchInput" placeholder="Buscar por raça, nome ou características...">
            <button class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>

        <div class="stats">
            <div class="stat-item"><h3>2000+</h3><p>Pets Adotados</p></div>
            <div class="stat-item"><h3>500+</h3><p>Famílias Felizes</p></div>
        </div>
    </header>

    <!-- Grid onde o JS vai jogar os cards -->
    <main class="pets-grid" id="pets-container">
        <p style="text-align:center; width: 100%; color: var(--text-muted);">Carregando amigos...</p>
    </main>

    <!-- Rodapé -->
    <footer style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 0.9rem;">
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>

    <!-- SEU JS NOVO -->
    <script src="public/js/home.js"></script>
    
    <!-- Script simples para a busca funcionar no front -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const termo = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.pet-card');
            
            cards.forEach(card => {
                const texto = card.innerText.toLowerCase();
                card.style.display = texto.includes(termo) ? 'block' : 'none';
            });
        });
        
    </script>
</body>
</html>