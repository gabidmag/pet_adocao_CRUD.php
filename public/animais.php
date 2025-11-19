<?php
session_start();
require_once 'conexao.php';

// Captura dos filtros vindos da URL. Usamos valores padronizados para evitar erros.
$busca    = trim($_GET['busca'] ?? '');
$especie  = $_GET['especie'] ?? 'todas';
$porte    = $_GET['porte'] ?? 'todos';
$genero   = $_GET['genero'] ?? 'todos';

try {
    // Monta a consulta incrementalmente para adicionar apenas os filtros utilizados.
    $sql = "SELECT * FROM animais WHERE 1=1";
    $params = [];

    if ($busca !== '') {
        $sql .= " AND (nome LIKE :busca OR raca LIKE :busca OR descricao LIKE :busca)";
        $params[':busca'] = "%{$busca}%";
    }

    if ($especie !== 'todas') {
        $sql .= " AND especie = :especie";
        $params[':especie'] = $especie;
    }

    if ($porte !== 'todos') {
        $sql .= " AND porte = :porte";
        $params[':porte'] = $porte;
    }

    if ($genero !== 'todos') {
        $sql .= " AND genero = :genero";
        $params[':genero'] = $genero;
    }

    $sql .= " ORDER BY destaque DESC, data_cadastro DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $animais = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Em caso de erro exibimos uma mensagem simples para não quebrar o layout.
    die('Erro ao buscar os pets: ' . $e->getMessage());
}

$opcoes_especie = ['todas' => 'Todos', 'Cachorro' => 'Cachorros', 'Gato' => 'Gatos'];
$opcoes_porte   = ['todos' => 'Todos', 'Pequeno' => 'Pequeno', 'Médio' => 'Médio', 'Grande' => 'Grande'];
$opcoes_genero  = ['todos' => 'Todos', 'Macho' => 'Macho', 'Fêmea' => 'Fêmea'];

require_once 'templates/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="index.php">Início</a> / Pets para adoção</p>
        <h1>Pets disponíveis</h1>
        <p>Use os filtros para encontrar o pet com a sua cara.</p>
    </div>
</section>

<section class="filter-section">
    <div class="container">
        <form class="filter-form" method="GET" action="animais.php">
            <label class="sr-only" for="busca">Buscar por nome ou raça</label>
            <input type="text" id="busca" name="busca" placeholder="Buscar por nome, raça ou característica" value="<?php echo htmlspecialchars($busca); ?>">

            <div class="filter-selects">
                <label>
                    Espécie
                    <select name="especie">
                        <?php foreach ($opcoes_especie as $valor => $rotulo): ?>
                            <option value="<?php echo $valor; ?>" <?php echo $especie === $valor ? 'selected' : ''; ?>><?php echo $rotulo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Porte
                    <select name="porte">
                        <?php foreach ($opcoes_porte as $valor => $rotulo): ?>
                            <option value="<?php echo $valor; ?>" <?php echo $porte === $valor ? 'selected' : ''; ?>><?php echo $rotulo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Gênero
                    <select name="genero">
                        <?php foreach ($opcoes_genero as $valor => $rotulo): ?>
                            <option value="<?php echo $valor; ?>" <?php echo $genero === $valor ? 'selected' : ''; ?>><?php echo $rotulo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                <button type="button" class="btn btn-secondary" id="limparFiltros">Limpar</button>
            </div>
        </form>

        <div class="filter-feedback" data-total="<?php echo count($animais); ?>">
            <strong><?php echo count($animais); ?></strong> pet(s) encontrado(s)
        </div>
    </div>
</section>

<section class="pet-listing-section">
    <div class="container">
        <?php if (count($animais) > 0): ?>
            <div class="pet-grid" data-js="gridPets">
                <?php foreach ($animais as $animal): ?>
                    <?php
                        $foto_pet = 'https://via.placeholder.com/400x350';
                        $caminho_foto = $animal['foto_path'] ?? '';

                        if (!empty($caminho_foto) && file_exists($caminho_foto)) {
                            $foto_pet = htmlspecialchars($caminho_foto);
                        }
                    ?>
                    <article class="pet-card" data-especie="<?php echo htmlspecialchars(strtolower($animal['especie'])); ?>" data-porte="<?php echo htmlspecialchars(strtolower($animal['porte'])); ?>">
                        <figure class="pet-card__image-container">
                            <img src="<?php echo $foto_pet; ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">

                            <?php if (!empty($animal['destaque'])): ?>
                                <span class="pet-card__tag destaque">Destaque</span>
                            <?php endif; ?>

                            <span class="pet-card__tag species <?php echo htmlspecialchars(strtolower($animal['especie'])); ?>">
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
                            <a href="detalhe-animal.php?id=<?php echo $animal['id']; ?>" class="btn btn-primary">Ver detalhes</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-pets-found">
                <p>Nenhum pet com esses filtros. Tente buscar de outra forma.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="js/filtro.js"></script>
<?php require_once 'templates/footer.php'; ?>
