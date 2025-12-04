<?php
    require_once '../conexao.php';
    session_start();

    // ID da URL e busca o pet
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) { header('Location: ../index.php'); exit; }

    $sql = "SELECT * FROM animais WHERE id = $id LIMIT 1";
    $result = mysqli_query($mysqli, $sql);
    if (!$result || mysqli_num_rows($result) === 0) { header('Location: ../index.php'); exit; }
    
    $pet = mysqli_fetch_assoc($result);
    
    
    $imgSrc = '../public/uploads/' . basename($pet['foto']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        </div>
    </nav>

    <!-- Seção de Detalhes -->
    <div class="detail-container">
        
        <!-- Coluna da Foto -->
        <div class="detail-image-wrapper">
            <img src="<?php echo $imgSrc; ?>" alt="Foto de <?php echo htmlspecialchars($pet['nome']); ?>" onerror="this.src='https://placehold.co/500?text=Foto+Indisponível'">
        </div>
        
        <!-- Coluna das Informações -->
        <div class="detail-info">
            
            <!-- CABEÇALHO (Nome e Espécie) -->
            <div class="detail-header">
                <h1><?php echo htmlspecialchars($pet['nome']); ?></h1>
                <span class="badge <?php echo strtolower($pet['especie']) == 'gato' ? 'cat' : 'dog'; ?>">
                    <?php echo htmlspecialchars($pet['especie']); ?>
                </span>
            </div>

            <p class="detail-breed"><?php echo htmlspecialchars($pet['raca']); ?></p>

            <!-- A GRADE DE DETALHES QUE ESTAVA FALTANDO -->
            <div class="info-grid">
                <div class="info-item"><i class="fa-solid fa-venus-mars"></i> <span><strong>Gênero:</strong> <?php echo htmlspecialchars($pet['genero']); ?></span></div>
                <div class="info-item"><i class="fa-solid fa-cake-candles"></i> <span><strong>Idade:</strong> <?php echo htmlspecialchars($pet['idade_anos']); ?> anos</span></div>
                <div class="info-item"><i class="fa-solid fa-ruler-vertical"></i> <span><strong>Porte:</strong> <?php echo htmlspecialchars($pet['porte']); ?></span></div>
                <div class="info-item"><i class="fa-solid fa-location-dot"></i> <span><strong>Local:</strong> <?php echo htmlspecialchars($pet['localizacao']); ?></span></div>
            </div>

            <!-- DESCRIÇÃO COMPLETA -->
            <div class="detail-description">
                <h3>Sobre <?php echo htmlspecialchars($pet['nome']); ?>:</h3>
                <p><?php echo nl2br(htmlspecialchars($pet['descricao'])); ?></p>
            </div>
            
            <!-- MENSAGEM DE SUCESSO/ERRO DO FORMULÁRIO -->
            <?php if(isset($_SESSION['adocao_msg'])): ?>
                <div class="alert-error" style="background-color:#D1FAE5; color:#065F46; margin: 20px 0;">
                    <?php echo $_SESSION['adocao_msg']; unset($_SESSION['adocao_msg']); ?>
                </div>
            <?php endif; ?>
            
            <!-- BOTÃO E TAXA DE ADOÇÃO -->
            <div class="detail-adoption">
                <div>
                    <span class="taxa-label">Taxa de Adoção</span>
                    <span class="price">R$ <?php echo number_format($pet['taxa_adocao'], 2, ',', '.'); ?></span>
                </div>
                <button id="btn-mostrar-form" class="btn-details" style="font-size: 1.1rem; padding: 15px 30px;">
                    <i class="fa-solid fa-heart"></i> Quero Adotar!
                </button>
            </div>

            <!-- FORMULÁRIO DE ADOÇÃO ESCONDIDO -->
            <div id="form-adocao-wrapper" style="display:none; margin-top: 30px; background-color: var(--bg-light); padding: 20px; border-radius: 12px;">
                <h4>Preencha seus dados para solicitar a adoção</h4>
                <form action="adotar.php" method="POST" class="floating-label-form">
                    <input type="hidden" name="animal_id" value="<?php echo $pet['id']; ?>">
                    <div class="input-group"><input type="text" name="nome_adotante" placeholder=" " required><label>Seu Nome Completo</label></div>
                    <div class="input-group"><input type="email" name="email_adotante" placeholder=" " required><label>Seu Melhor Email</label></div>
                    <div class="input-group"><input type="text" name="telefone_adotante" placeholder=" " required><label>Seu Telefone (com DDD)</label></div>
                    <div class="input-group"><textarea name="motivo_adocao" rows="3" placeholder=" "></textarea><label>Por que você quer adotar este pet?</label></div>
                    <button type="submit" class="btn-login">Enviar Pedido</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT PARA MOSTRAR/ESCONDER O FORMULÁRIO -->
    <script>
        const btn = document.getElementById('btn-mostrar-form');
        const formWrapper = document.getElementById('form-adocao-wrapper');
        
        btn.addEventListener('click', function() {
            if (formWrapper.style.display === 'none') {
                formWrapper.style.display = 'block';
                btn.innerHTML = '<i class="fa-solid fa-times"></i> Cancelar';
                btn.style.backgroundColor = '#EF4444'; // Cor de cancelar
            } else {
                formWrapper.style.display = 'none';
                btn.innerHTML = '<i class="fa-solid fa-heart"></i> Quero Adotar!';
                btn.style.backgroundColor = 'var(--primary-color)'; // Volta a cor original
            }
        });
    </script>

</body>
</html>