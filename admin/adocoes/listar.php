<?php
    
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $pedidos = [];
    $erro = '';

    try {
        $sql = "SELECT 
                    a.id, a.nome_adotante, a.email_adotante, a.status, a.data_pedido,
                    p.nome AS nome_animal, p.id AS animal_id
                FROM adocoes a
                JOIN animais p ON a.animal_id = p.id
                ORDER BY a.data_pedido DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $pedidos = $stmt->fetchAll();

    } catch (PDOException $e) {
        $erro = "❌ Erro ao carregar a lista de pedidos: " . $e->getMessage();
    }

    $mensagem = '';
    if (isset($_SESSION['mensagem_sucesso'])) {
        $mensagem = "<div class='alerta sucesso'>" . $_SESSION['mensagem_sucesso'] . "</div>";
        unset($_SESSION['mensagem_sucesso']);
    }
    if (isset($_SESSION['mensagem_erro'])) {
        $mensagem = "<div class='alerta erro'>" . $_SESSION['mensagem_erro'] . "</div>";
        unset($_SESSION['mensagem_erro']);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedidos de Adoção - Área Administrativa</title>
    <link rel="stylesheet" href="../../public/css/style.css"> 
    <style>
        .status-pendente { color: orange; font-weight: bold; }
        .status-aprovada { color: green; font-weight: bold; }
        .status-rejeitada { color: red; }
    </style>
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="../index.php">Início</a> |
            <a href="../animais/listar.php">Gerenciar Animais</a> |
            <a href="../../logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Pedidos de Adoção</h2>

        <?php echo $mensagem; ?>
        <?php if ($erro): ?>
            <div class='alerta erro'><?php echo $erro; ?></div>
        <?php endif; ?>

        <?php if (count($pedidos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Adotante</th>
                        <th>Email</th>
                        <th>Animal</th>
                        <th>Data Pedido</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido->id); ?></td>
                            <td><?php echo htmlspecialchars($pedido->nome_adotante); ?></td>
                            <td><?php echo htmlspecialchars($pedido->email_adotante); ?></td>
                            <td><a href="../animais/editar.php?id=<?php echo $pedido->animal_id; ?>"><?php echo htmlspecialchars($pedido->nome_animal); ?></a></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pedido->data_pedido)); ?></td>
                            <td class="status-<?php echo htmlspecialchars($pedido->status); ?>">
                                <?php echo ucfirst(htmlspecialchars($pedido->status)); ?>
                            </td>
                            <td>
                                <a href="visualizar.php?id=<?php echo $pedido->id; ?>">Visualizar</a> |
                                <a href="deletar.php?id=<?php echo $pedido->id; ?>" onclick="return confirm('Tem certeza que deseja deletar este pedido?');">Deletar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum pedido de adoção encontrado.</p>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>