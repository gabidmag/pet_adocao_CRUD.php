<?php
session_start();
require_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die('Pet não encontrado.');
}

try {
    $stmt = $pdo->prepare('SELECT * FROM animais WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro ao carregar informações do pet.');
}

if (!$animal) {
    die('Pet não encontrado.');
}

$foto_pet = 'https://via.placeholder.com/500x400';
$caminho_foto = $animal['foto_path'] ?? '';

if (!empty($caminho_foto) && file_exists($caminho_foto)) {
    $foto_pet = htmlspecialchars($caminho_foto);
}

require_once 'templates/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="animais.php">Pets</a> / <?php echo htmlspecialchars($animal['nome']); ?></p>
        <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
        <p>Veja mais detalhes sobre esse pet e avance para o pedido de adoção.</p>
    </div>
</section>

<section class="pet-detail">
    <div class="container pet-detail__grid">
        <figure class="pet-detail__photo">
            <img src="<?php echo $foto_pet; ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
            <figcaption>
                <?php echo htmlspecialchars($animal['nome']); ?>, <?php echo htmlspecialchars($animal['raca']); ?>
            </figcaption>
        </figure>

        <div class="pet-detail__info">
            <div class="pet-detail__tags">
                <span class="badge especie"><?php echo htmlspecialchars($animal['especie']); ?></span>
                <span class="badge porte"><?php echo htmlspecialchars($animal['porte']); ?></span>
                <span class="badge genero"><?php echo htmlspecialchars($animal['genero']); ?></span>
            </div>

            <p class="pet-detail__description">
                <?php echo !empty($animal['descricao']) ? nl2br(htmlspecialchars($animal['descricao'])) : 'Ainda não temos uma descrição completa para esse pet, mas ele está ansioso para conhecer um novo lar!'; ?>
            </p>

            <ul class="pet-detail__list">
                <li><strong>Idade:</strong> <?php echo htmlspecialchars($animal['idade_anos']); ?> ano(s)</li>
                <li><strong>Localização:</strong> <?php echo htmlspecialchars($animal['localizacao']); ?></li>
                <li><strong>Data de cadastro:</strong> <?php echo date('d/m/Y', strtotime($animal['data_cadastro'])); ?></li>
            </ul>

            <div class="pet-detail__actions">
                <a href="adotar.php?id=<?php echo $animal['id']; ?>" class="btn btn-primary">Quero adotar</a>
                <a href="animais.php" class="btn btn-secondary">Voltar para a lista</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'templates/footer.php'; ?>
