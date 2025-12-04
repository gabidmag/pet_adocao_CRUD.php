<?php

require_once 'conexao.php';


if(!isset($_SESSION)) {
    session_start();
}


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
    
            <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin'): ?>
               <!-- Se for ADMIN LOGADO -->
               <li><a href="#pets-container">Nossos Pets</a></li>
               <li><a href="admin/index.php" class="btn-details">Painel Admin</a></li>
               <li><a href="logout.php" style="color:#EF4444; font-weight:bold;">Sair</a></li>

            <?php elseif (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'usuario'): ?>
                <!-- Se for USUÁRIO COMUM LOGADO -->
                <li><a href="#pets-container">Nossos Pets</a></li>
                <li><a href="logout_public.php" style="color:#EF4444; font-weight:bold;">Sair</a></li>

            <?php else: ?>
               <!-- Se for VISITANTE -->
               <li><a href="#pets-container">Nossos Pets</a></li>
               <li><a href="login.php" class="btn-details">Área Restrita (Admin)</a></li>
            <?php endif; ?>
    
        </ul>


        <div class="nav-actions">

    <?php if (isset($_SESSION['id_usuario'])): ?>
        
        <!-- SE O USUÁRIO ESTIVER LOGADO -->
        <div class="user-status">
            
            <!-- Mostra o nome do usuário -->
            <span class="user-name-display">
                <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?>
            </span>
            
            <!-- Ícone  verde -->
            <div class="btn-icon" style="position: relative;">
                <i class="fa-solid fa-user"></i>
                <div class="online-indicator"></div>
            </div>
            
        </div>

    <?php else: ?>

        <!-- SE FOR VISITANTE (NÃO LOGADO) -->
        <a href="login_public.php" class="btn-icon" title="Login de Usuário">
            <i class="fa-regular fa-user"></i>
        </a>
        
    <?php endif; ?>

    <!-- Botão de Tema -->
    <button id="theme-toggle" class="btn-icon" title="Alternar Tema">
        <i class="fa-solid fa-sun"></i>
    </button>
    
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

<!-- ===== MODAL/POP-UP DE DOAÇÃO ===== -->
<div id="doacao-modal" class="modal-overlay">
    <div class="modal-content">
        <button id="close-modal" class="close-btn">&times;</button>
        
        <div class="modal-icon">
            <i class="fa-solid fa-hand-holding-heart"></i>
        </div>
        
        <h2>Quer doar um pet?</h2>
        <p>Se você resgatou um animalzinho e precisa de ajuda para encontrar um lar para ele, fale conosco! Teremos prazer em ajudar no processo de adoção.</p>
        
        <div class="modal-contato">
            <i class="fa-brands fa-whatsapp"></i>
            <span>(83) 99999-8888</span>
        </div>
        
        <p class="modal-footer-text">Entre em contato e ajude a salvar uma vida!</p>
    </div>
</div>

</body>
</html>

</body>
</html>