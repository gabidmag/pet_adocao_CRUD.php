<?php
    // Volta uma pasta para encontrar os arquivos na raiz
    require_once '../conexao.php';
    session_start(); // Para a navbar saber se está logado

    // Pega o ID da URL e garante que é um número
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Se não for um ID válido, manda de volta para a home
    if ($id <= 0) {
        header('Location: ../index.php');
        exit;
    }

    // Busca os dados APENAS do animal com esse ID
    $sql = "SELECT * FROM animais WHERE id = $id LIMIT 1";
    $result = mysqli_query($mysqli, $sql);
    
    // Se não encontrou o pet, manda de volta para a home
    if (!$result || mysqli_num_rows($result) === 0) {
        header('Location: ../index.php');
        exit;
    }
    
    // Armazena os dados do pet na variável $pet
    $pet = mysqli_fetch_assoc($result);

    // Corrige o caminho da imagem para ser usado no HTML
    $imgSrc = '../public/uploads/' . basename($pet['foto']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes de <?php echo htmlspecialchars($pet['nome']); ?></title>
    <!-- CSS e Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar Padrão -->
    <nav class="navbar">
        <a href="../index.php" class="logo"><i class="fa-solid fa-heart"></i> PetAdopt</a>
        <div class="nav-links">
            <a href="../index.php">Voltar para Home</a>
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="../admin/index.php" class="btn-details">Painel Admin</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Seção de Detalhes -->
    <div class="detail-container">
        
        <!-- Coluna da Foto -->
        <div class="detail-image-wrapper">
            <img src="<?php echo $imgSrc; ?>" alt="Foto de <?php echo htmlspecialchars($pet['nome']); ?>">
        </div>
        
        <!-- Coluna das Informações -->
        <div class="detail-info">
            
            <div class="detail-header">
                <h1><?php echo htmlspecialchars($pet['nome']); ?></h1>
                <span class="badge <?php echo strtolower($pet['especie']) == 'gato' ? 'cat' : 'dog'; ?>">
                    <?php echo htmlspecialchars($pet['especie']); ?>
                </span>
            </div>

            <p class="detail-breed"><?php echo htmlspecialchars($pet['raca']); ?></p>

            <div class="info-grid">
                <div class="info-item"><i class="fa-solid fa-venus-mars"></i> <span><strong>Gênero:</strong> <?php echo htmlspecialchars($pet['genero']); ?></span></div>
                <div class="info-item"><i class="fa-solid fa-cake-candles"></i> <span><strong>Idade:</strong> <?php echo htmlspecialchars($pet['idade_anos']); ?> anos</span></div>
                <div class="info-item"><i class="fa-solid fa-ruler-vertical"></i> <span><strong>Porte:</strong> <?php echo htmlspecialchars($pet['porte']); ?></span></div>
                <div class="info-item"><i class="fa-solid fa-location-dot"></i> <span><strong>Local:</strong> <?php echo htmlspecialchars($pet['localizacao']); ?></span></div>
            </div>

            <div class="detail-description">
                <h3>Sobre <?php echo htmlspecialchars($pet['nome']); ?>:</h3>
                <p><?php echo nl2br(htmlspecialchars($pet['descricao'])); ?></p>
            </div>

            <div class="detail-adoption">
                <div>
                    <span class="taxa-label">Taxa de Adoção</span>
                    <span class="price">R$ <?php echo number_format($pet['taxa_adocao'], 2, ',', '.'); ?></span>
                </div>
                <a href="#" class="btn-details" style="font-size: 1.1rem; padding: 15px 30px;">
                    <i class="fa-solid fa-heart"></i> Quero Adotar!
                </a>
            </div>

        </div>
    </div>
</body>
</html>