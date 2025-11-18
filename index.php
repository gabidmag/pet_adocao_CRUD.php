<?php
    require_once 'conexao.php'; 

    
    $animais_destaque = [];
    try {
        $stmt = $pdo->query("SELECT id, nome, foto FROM animais WHERE status = 'disponivel' LIMIT 3");
        $animais_destaque = $stmt->fetchAll();
    } catch (PDOException $e) {
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pet Adoção - Encontre seu Melhor Amigo</title>
    <link rel="stylesheet" href="public/css/style.css"> 
</head>
<body>
    <header>
        <nav>
            <h1>Pet Adoção</h1>
            <a href="index.php">Início</a>
            <a href="public/animais.php">Nossos Pets</a>
            <a href="login.php">Área Restrita</a> 
        </nav>
    </header>

    <main class="container">
        <h2>Adote, Não Compre.</h2>
        <p>Milhares de animais esperam por um lar. Encontre seu novo melhor amigo hoje!</p>
        
        <div class="destaque">
            <a href="public/animais.php" class="botao-principal">Ver Pets Disponíveis</a>
        </div>
        
        <?php if (!empty($animais_destaque)): ?>
        <section class="destaques-home">
            <h3>Pets em Destaque</h3>
            </section>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>