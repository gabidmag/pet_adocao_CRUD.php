<?php
session_start();
require_once 'conexao.php';

try {
    $query = "SELECT * FROM animais ORDER BY destaque DESC, data_cadastro DESC";
    $stmt = $pdo->query($query);
    $animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar os animais: " . $e->getMessage());
}

require_once 'templates/header.php';
?>

<section class="hero-section">
    <div class="container">
        <h1>Adote um Novo Amigo</h1>
        <p>Milhares de animais estão esperando por um lar cheio de amor...</p>
        <form class="search-form" action="animais.php" method="GET">
            <input type="text" name="busca" placeholder="Buscar por raça, nome ou características..." aria-label="Campo de busca de pets">
            <button type="submit" aria-label="Buscar"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<section class="pet-listing-section">
    <div class="container">
        <h2 class="section-title">Encontre seu Companheiro</h2>
        
        <?php if (count($animais) > 0): ?>
            <div class="pet-grid">
                <?php foreach ($animais as $animal): ?>
                    <div class="pet-card">
                        <?php
                        $foto_pet = 'https://via.placeholder.com/400x350';
                        $caminho_foto = $animal['foto_path'];

                        if (!empty($caminho_foto) && file_exists($caminho_foto)) {
                            $foto_pet = htmlspecialchars($caminho_foto);
                        }
                        ?>
                        <figure class="pet-card__image-container">
                            <img src="<?php echo $foto_pet; ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
                            
                            <?php if ($animal['destaque']): ?>
                                <span class="pet-card__tag destaque">Destaque</span>
                            <?php endif; ?>

                            <span class="pet-card__tag species <?php echo strtolower(htmlspecialchars($animal['especie'])); ?>">
                                <?php echo htmlspecialchars($animal['especie']); ?>
                            </span>
                        </figure>
                        <div class="pet-card__content">
                            <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                            <p class="pet-card__breed"><?php echo htmlspecialchars($animal['raca']); ?></p>
                            <div class="pet-card__details">
                                <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($animal['idade_anos']); ?> ano(s)</span>
                                <span><i class="fas fa-venus-mars"></i> <?php echo htmlspecialchars($animal['genero']); ?></span>
                                <span><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($animal['porte']); ?></span>
                            </div>
                            <p class="pet-card__location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($animal['localizacao']); ?>
                            </p>
                            <a href="detalhe-animal.php?id=<?php echo $animal['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-pets-found">
                <p>Nenhum animalzinho encontrado. Volte em breve!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'templates/footer.php';
?>