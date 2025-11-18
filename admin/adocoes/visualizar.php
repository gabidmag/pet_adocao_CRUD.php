<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $mensagem = '';
    $pedido = null;
    $pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$pedido_id) {
        header('Location: listar.php');
        exit();
    }

    // 1. Lógica para atualizar o status (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novo_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if (in_array($novo_status, ['pendente', 'aprovada', 'rejeitada'])) {
            try {
                $sql_update_pedido = "UPDATE adocoes SET status = :status WHERE id = :id";
                $stmt_update_pedido = $pdo->prepare($sql_update_pedido);
                $stmt_update_pedido->bindParam(':status', $novo_status);
                $stmt_update_pedido->bindParam(':id', $pedido_id);
                $stmt_update_pedido->execute();
                
                if ($novo_status == 'aprovada') {
                    $sql_update_animal = "UPDATE animais SET status = 'adotado' WHERE id = (SELECT animal_id FROM adocoes WHERE id = :id)";
                    $stmt_update_animal = $pdo->prepare($sql_update_animal);
                    $stmt_update_animal->bindParam(':id', $pedido_id);
                    $stmt_update_animal->execute();
                }
        
                if ($novo_status == 'rejeitada') {
                    $sql_update_animal = "UPDATE animais SET status = 'disponivel' WHERE id = (SELECT animal_id FROM adocoes WHERE id = :id)";
                    $stmt_update_animal = $pdo->prepare($sql_update_animal);
                    $stmt_update_animal->bindParam(':id', $pedido_id);
                    $stmt_update_animal->execute();
                }


                $mensagem = "✅ Status do pedido atualizado para: " . ucfirst($novo_status);

            } catch (PDOException $e) {
                $mensagem = "❌ Erro ao atualizar status: " . $e->getMessage();
            }
        } else {
            $mensagem = "❌ Status inválido fornecido.";
        }
    }


    // 2. Lógica para carregar os dados do pedido e do animal
    try {
        $sql = "SELECT 
                    a.*,
                    p.nome AS nome_animal, p.especie, p.raca
                FROM adocoes a
                JOIN animais p ON a.animal_id = p.id
                WHERE a.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $pedido_id);
        $stmt->execute();
        
        $pedido = $stmt->fetch();

        if (!$pedido) {
            $mensagem = "❌ Pedido de adoção não encontrado.";
            header('Refresh: 3; URL=listar.php'); 
        }

    } catch (PDOException $e) {
        $mensagem = "❌ Erro ao buscar pedido: " . $e->getMessage();
    }


    if ($pedido):
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Pedido #<?php echo $pedido->id; ?> - Área Administrativa</title>
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="../index.php">Início</a> |
            <a href="listar.php">Voltar para Pedidos</a> |
            <a href="../../logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Detalhes do Pedido #<?php echo $pedido->id; ?></h2>

        <?php 
        if ($mensagem) {
            $classe_alerta = strpos($mensagem, '✅') !== false ? 'sucesso' : 'erro';
            echo "<div class='alerta {$classe_alerta}'>{$mensagem}</div>";
        }
        ?>

        <section class="dados-adotante">
            <h3>Dados do Adotante</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido->nome_adotante); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido->email_adotante); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido->telefone_adotante); ?></p>
            <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido->data_pedido)); ?></p>
        </section>

        <section class="dados-animal">
            <h3>Animal Solicitado</h3>
            <p><strong>Nome do Pet:</strong> <a href="../animais/editar.php?id=<?php echo $pedido->animal_id; ?>"><?php echo htmlspecialchars($pedido->nome_animal); ?></a></p>
            <p><strong>Espécie:</strong> <?php echo htmlspecialchars($pedido->especie); ?></p>
            <p><strong>Raça:</strong> <?php echo htmlspecialchars($pedido->raca); ?></p>
        </section>

        <section class="motivo-adocao">
            <h3>Motivação para Adoção</h3>
            <p><?php echo nl2br(htmlspecialchars($pedido->motivo_adocao)); ?></p>
        </section>

        <section class="gestao-status">
            <h3>Gerenciamento de Status</h3>
            <p><strong>Status Atual:</strong> <span class="status-<?php echo htmlspecialchars($pedido->status); ?>"><?php echo ucfirst(htmlspecialchars($pedido->status)); ?></span></p>

            <form method="POST" action="visualizar.php?id=<?php echo $pedido->id; ?>">
                <label for="status">Alterar Status:</label>
                <select id="status" name="status" required>
                    <option value="pendente" <?php echo ($pedido->status == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                    <option value="aprovada" <?php echo ($pedido->status == 'aprovada' ? 'selected' : ''); ?>>Aprovada</option>
                    <option value="rejeitada" <?php echo ($pedido->status == 'rejeitada' ? 'selected' : ''); ?>>Rejeitada</option>
                </select>
                <button type="submit">Atualizar Status</button>
            </form>
        </section>

        <p style="margin-top: 20px;"><a href="listar.php" class="botao-cancelar">Voltar para a Lista de Pedidos</a></p>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>
<?php endif; ?>