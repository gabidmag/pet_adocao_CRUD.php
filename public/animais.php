<?php
    require_once '../conexao.php';
    require_once '../verifica-login.php';

    $animais = [];
    $especies = [];
    $alertas = [];

    $especie = trim($_GET['especie'] ?? '');
    $status = trim($_GET['status'] ?? 'disponivel');
    $idade_min = $_GET['idade_min'] ?? '';
    $idade_max = $_GET['idade_max'] ?? '';

    $idade_min = ($idade_min === '' ? null : max(0, (int)$idade_min));
    $idade_max = ($idade_max === '' ? null : max(0, (int)$idade_max));

    $status_validos = ['disponivel', 'adotado', 'indisponivel'];
    $status_filtro = $status;

    if ($status === 'todos') {
        $status_filtro = '';
    } elseif ($status_filtro === '' || !in_array($status_filtro, $status_validos, true)) {
        $status = 'disponivel';
        $status_filtro = 'disponivel';
    }

    if ($idade_min !== null && $idade_max !== null && $idade_min > $idade_max) {
        $alertas[] = 'A idade mínima não pode ser maior que a idade máxima.';
        $idade_min = null;
        $idade_max = null;
    }

    $sql = "SELECT id, nome, especie, raca, idade, descricao, foto, status 
            FROM animais 
            WHERE 1=1";

    if ($status_filtro !== '') {
        $status_safe = mysqli_real_escape_string($mysqli, $status_filtro);
        $sql .= " AND status = '{$status_safe}'";
    }

    if ($especie !== '') {
        $especie_safe = mysqli_real_escape_string($mysqli, $especie);
        $sql .= " AND especie = '{$especie_safe}'";
    }

    if ($idade_min !== null) {
        $sql .= " AND idade >= " . (int) $idade_min;
    }

    if ($idade_max !== null) {
        $sql .= " AND idade <= " . (int) $idade_max;
    }

    $sql .= " ORDER BY data_cadastro DESC";

    $resultado = mysqli_query($mysqli, $sql);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $animais[] = $row;
        }
        mysqli_free_result($resultado);
    } else {
        $alertas[] = 'Erro ao carregar os animais: ' . mysqli_error($mysqli);
    }

    $sql_especies = "SELECT DISTINCT especie 
                     FROM animais 
                     WHERE especie IS NOT NULL AND especie <> '' 
                     ORDER BY especie ASC";

    $resultado_especies = mysqli_query($mysqli, $sql_especies);

    if ($resultado_especies) {
        while ($row = mysqli_fetch_assoc($resultado_especies)) {
            $especies[] = $row['especie'];
        }
        mysqli_free_result($resultado_especies);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pets Disponíveis - Pet Adoção</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filtros { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .filtros label { display: block; margin-bottom: 10px; }
        .lista-animais { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .animal-card { border: 1px solid #ddd; border-radius: 6px; padding: 15px; background: #fff; }
        .animal-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .animal-card h3 { margin: 5px 0; }
        .animal-card p { margin: 4px 0; font-size: 0.95rem; }
        .animal-card a { display: inline-block; margin-top: 10px; color: #007bff; text-decoration: none; }
        .animal-card a:hover { text-decoration: underline; }
        .alerta { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alerta.erro { background: #fdecea; color: #c0392b; }
        .alerta.vazio { background: #f7f7f7; color: #555; }
    </style>
</head>
<body>
    <header>
        <nav>
            <h1>Pet Adoção</h1>
            <a href="../index.php">Início</a>
            <a href="animais.php">Nossos Pets</a>
            <?php if (is_logged_in()): ?>
                <a href="../logout.php">Sair</a>
            <?php else: ?>
                <a href="../login.php">Área Restrita</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <h2>Pets disponíveis para adoção</h2>
        <p>Use os filtros para encontrar o pet ideal para sua família.</p>

        <?php foreach ($alertas as $mensagem): ?>
            <div class="alerta erro"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endforeach; ?>

        <form method="GET" class="filtros">
            <label>
                Espécie:
                <select name="especie">
                    <option value="">Todas</option>
                    <?php foreach ($especies as $opcao): ?>
                        <option value="<?php echo htmlspecialchars($opcao); ?>" <?php echo ($opcao === $especie ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($opcao); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Status:
                <select name="status">
                    <option value="todos" <?php echo ($status === 'todos' ? 'selected' : ''); ?>>Todos</option>
                    <option value="disponivel" <?php echo ($status === 'disponivel' ? 'selected' : ''); ?>>Disponível</option>
                    <option value="adotado" <?php echo ($status === 'adotado' ? 'selected' : ''); ?>>Adotado</option>
                    <option value="indisponivel" <?php echo ($status === 'indisponivel' ? 'selected' : ''); ?>>Indisponível</option>
                </select>
            </label>

            <label>
                Idade mínima:
                <input type="number" name="idade_min" min="0" value="<?php echo ($idade_min !== null ? (int)$idade_min : ''); ?>">
            </label>

            <label>
                Idade máxima:
                <input type="number" name="idade_max" min="0" value="<?php echo ($idade_max !== null ? (int)$idade_max : ''); ?>">
            </label>

            <button type="submit">Aplicar filtros</button>
            <a href="animais.php">Limpar filtros</a>
        </form>

        <?php if (count($animais) > 0): ?>
            <section class="lista-animais">
                <?php foreach ($animais as $animal): ?>
                    <article class="animal-card">
                        <?php if (!empty($animal['foto'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($animal['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
                        <?php else: ?>
                            <div class="alerta vazio">Foto não disponível</div>
                        <?php endif; ?>

                        <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                        <p><strong>Espécie:</strong> <?php echo htmlspecialchars($animal['especie'] ?? 'Não informada'); ?></p>
                        <p><strong>Raça:</strong> <?php echo htmlspecialchars($animal['raca'] ?? 'Não informada'); ?></p>
                        <p><strong>Idade:</strong> <?php echo ($animal['idade'] !== null ? (int)$animal['idade'] . ' ano(s)' : 'Não informada'); ?></p>
                        <?php if (!empty($animal['descricao'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($animal['descricao'])); ?></p>
                        <?php else: ?>
                            <p>Descrição não informada.</p>
                        <?php endif; ?>
                        <a href="detalhe-animal.php?id=<?php echo (int)$animal['id']; ?>">Ver detalhes</a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="alerta vazio">
                Nenhum pet encontrado com os filtros selecionados. Tente novamente com outros critérios.
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>