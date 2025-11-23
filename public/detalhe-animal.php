<?php
session_start();
require_once 'conexao.php';

// 1. Pega o ID da URL e valida
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$animal = null;

if ($id) {
    // 2. Prepara e executa a consulta para buscar UM animal específico
    try {
        $query = "SELECT * FROM animais WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // 3. Pega os dados do animal
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erro ao buscar o animal: " . $e->getMessage());
    }
}

// Se o animal não for encontrado (ID inválido ou não existe), o usuário será informado no HTML
require_once 'templates/header.php';
?>

<div class="container page-container">

    <?php if ($animal): // 4. Se o animal foi encontrado, exibe os detalhes ?>

        <div class="pet-detail-container">
            <div class="pet-detail-image">
                <?php
                    $foto_pet = 'https://via.placeholder.com/600x500';
                    $caminho_foto = $animal['foto_path'];
                    if (!empty($caminho_foto) && file_exists($caminho_foto)) {
                        $foto_pet = htmlspecialchars($caminho_foto);
                    }
                ?>
                <img src="<?php echo $foto_pet; ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
            </div>

            <div class="pet-detail-info">
                <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
                <p class="pet-detail-breed"><?php echo htmlspecialchars($animal['raca']); ?></p>

                <div class="info-section">
                    <h3>Características</h3>
                    <ul>
                        <li><strong>Espécie:</strong> <?php echo htmlspecialchars($animal['especie']); ?></li>
                        <li><strong>Sexo:</strong> <?php echo htmlspecialchars($animal['genero']); ?></li>
                        <li><strong>Porte:</strong> <?php echo htmlspecialchars($animal['porte']); ?></li>
                        <li>
                            <strong>Idade:</strong>
                            <?php
                                $idade_texto = '';
                                $anos = $animal['idade_anos'];
                                $meses = $animal['idade_meses'];
                                if ($anos > 0) $idade_texto .= $anos . ' ' . ($anos > 1 ? 'anos' : 'ano');
                                if ($meses > 0) {
                                    if ($anos > 0) $idade_texto .= ' e ';
                                    $idade_texto .= $meses . ' ' . ($meses > 1 ? 'meses' : 'mês');
                                }
                                if (empty($idade_texto)) $idade_texto = 'Menos de 1 mês';
                                echo htmlspecialchars($idade_texto);
                            ?>
                        </li>
                    </ul>
                </div>
                
                <div class="info-section">
                    <h3>Localização</h3>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($animal['localizacao']); ?></p>
                </div>

                <div class="info-section">
                    <h3>Minha História</h3>
                    <p><?php echo nl2br(htmlspecialchars($animal['historia'])); // nl2br para preservar quebras de linha ?></p>
                </div>

                <div class="adoption-cta">
                    <p class="adoption-fee">Taxa de Adoção: <strong>R$ <?php echo number_format($animal['taxa_adocao'], 2, ',', '.'); ?></strong></p>
                    <a href="#" class="btn btn-primary btn-large">Quero Adotar!</a>
                </div>
            </div>
        </div>

    <?php else: // 5. Se o animal NÃO foi encontrado, exibe uma mensagem de erro ?>

        <div class="not-found-container">
            <h2>Oops! Animal não encontrado.</h2>
            <p>O pet que você está procurando não existe ou o link está incorreto.</p>
            <a href="index.php" class="btn btn-primary">Voltar para a Página Inicial</a>
        </div>

    <?php endif; ?>

</div>

<?php
require_once 'templates/footer.php';
?>