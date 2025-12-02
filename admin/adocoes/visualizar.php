<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php');

    $mensagem = '';
    $pedido = null;

    // Pega o ID do pedido
    $pedido_id = $_GET['id'] ?? 0;

    if (!$pedido_id) {
        header('Location: listar.php');
        exit();
    }

    // Atualiza status se for POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novo_status = $_POST['status'] ?? '';
        
        if (in_array($novo_status, ['pendente', 'aprovada', 'rejeitada'])) {
            // Atualiza pedido
            $sql = "UPDATE adocoes SET status = '$novo_status' WHERE id = $pedido_id";
            if (mysqli_query($mysqli, $sql)) {
                
                // Atualiza animal se for aprovado ou rejeitado
                if ($novo_status == 'aprovada') {
                    $sql_animal = "UPDATE animais SET status = 'adotado' WHERE id = (SELECT animal_id FROM adocoes WHERE id = $pedido_id)";
                    mysqli_query($mysqli, $sql_animal);
                } 
                elseif ($novo_status == 'rejeitada') {
                    $sql_animal = "UPDATE animais SET status = 'disponivel' WHERE id = (SELECT animal_id FROM adocoes WHERE id = $pedido_id)";
                    mysqli_query($mysqli, $sql_animal);
                }
                
                $mensagem = "✅ Status atualizado para: " . ucfirst($novo_status);
            } else {
                $mensagem = "❌ Erro ao atualizar: " . mysqli_error($mysqli);
            }
        }
    }

    // Busca dados do pedido
    $sql = "SELECT a.*, p.nome AS nome_animal, p.especie, p.raca 
            FROM adocoes a 
            JOIN animais p ON a.animal_id = p.id 
            WHERE a.id = $pedido_id";
            
    $resultado = mysqli_query($mysqli, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $pedido = mysqli_fetch_assoc($resultado);
    } else {
        $mensagem = "❌ Pedido não encontrado";
        header('Refresh: 3; URL=listar.php');
        exit();
    }
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