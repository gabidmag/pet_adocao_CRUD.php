<?php
    require_once '../conexao.php';
    require_once '../verifica-login.php';

    $animal = null;
    $alertas = [];

    $mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? '';
    $mensagem_erro = $_SESSION['mensagem_erro'] ?? '';
    unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);

    $animal_id_param = $_GET['id'] ?? '';

    if ($animal_id_param === '' || ctype_digit($animal_id_param) === false) {
        $alertas[] = 'Código do animal inválido.';
        http_response_code(400);
    } else {
        $animal_id = (int) $animal_id_param;
        $sql = "SELECT id, nome, especie, raca, idade, descricao, foto, status 
                FROM animais 
                WHERE id = ? 
                LIMIT 1";

        $stmt = mysqli_prepare($mysqli, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $animal_id);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($resultado && mysqli_num_rows($resultado) === 1) {
                $animal = mysqli_fetch_assoc($resultado);
            } else {
                $alertas[] = 'Animal não encontrado ou removido.';
                http_response_code(404);
            }

            if ($resultado) {
                mysqli_free_result($resultado);
            }

            mysqli_stmt_close($stmt);
        } else {
            $alertas[] = 'Erro ao buscar os dados do animal: ' . mysqli_error($mysqli);
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Animal - Pet Adoção</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .detalhes { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 30px; }
        .detalhes img { width: 100%; max-height: 360px; object-fit: cover; border-radius: 6px; }
        .alerta { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alerta.sucesso { background: #e3f7e6; color: #1e7e34; }
        .alerta.erro { background: #fdecea; color: #c0392b; }
        .alerta.info { background: #f7f7f7; color: #555; }
        .form-adocao { border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin-top: 30px; }
        .form-adocao label { display: block; margin-bottom: 10px; }
        .form-adocao input, .form-adocao textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-adocao button { margin-top: 10px; }
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
        <a href="animais.php">&larr; Voltar para a lista</a>
        <h2>Detalhes do Pet</h2>

        <?php if ($mensagem_sucesso): ?>
            <div class="alerta sucesso"><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
            <div class="alerta erro"><?php echo htmlspecialchars($mensagem_erro); ?></div>
        <?php endif; ?>

        <?php foreach ($alertas as $mensagem): ?>
            <div class="alerta erro"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endforeach; ?>

        <?php if ($animal): ?>
            <section class="detalhes">
                <div>
                    <?php if (!empty($animal['foto'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($animal['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($animal['nome']); ?>">
                    <?php else: ?>
                        <div class="alerta info">Foto não disponível.</div>
                    <?php endif; ?>
                </div>
                <div>
                    <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                    <p><strong>Espécie:</strong> <?php echo htmlspecialchars($animal['especie'] ?? 'Não informada'); ?></p>
                    <p><strong>Raça:</strong> <?php echo htmlspecialchars($animal['raca'] ?? 'Não informada'); ?></p>
                    <p><strong>Idade:</strong> <?php echo ($animal['idade'] !== null ? (int)$animal['idade'] . ' ano(s)' : 'Não informada'); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($animal['status'])); ?></p>
                    <p><strong>Descrição:</strong><br><?php echo nl2br(htmlspecialchars($animal['descricao'] ?? 'Sem descrição cadastrada.')); ?></p>
                </div>
            </section>

            <?php if ($animal['status'] === 'disponivel'): ?>
                <section class="form-adocao">
                    <h3>Quero adotar o(a) <?php echo htmlspecialchars($animal['nome']); ?></h3>
                    <form method="POST" action="adotar.php">
                        <input type="hidden" name="animal_id" value="<?php echo (int)$animal['id']; ?>">

                        <label>
                            Nome completo
                            <input type="text" name="nome_adotante" required maxlength="150">
                        </label>

                        <label>
                            Email
                            <input type="email" name="email_adotante" required maxlength="150">
                        </label>

                        <label>
                            Telefone
                            <input type="text" name="telefone_adotante" required maxlength="50">
                        </label>

                        <label>
                            Por que deseja adotar?
                            <textarea name="motivo_adocao" rows="4" placeholder="Conte um pouco sobre sua motivação."></textarea>
                        </label>

                        <button type="submit">Enviar pedido de adoção</button>
                    </form>
                </section>
            <?php else: ?>
                <div class="alerta info" style="margin-top: 25px;">
                    Este pet não está disponível para novos pedidos de adoção no momento.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>