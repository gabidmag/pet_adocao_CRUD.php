<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php');

    $mensagem = '';
    $pedido_id = $_GET['id'] ?? 0;
    if (!$pedido_id) { header('Location: listar.php'); exit(); }

    // Atualiza status se for POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $novo_status = $_POST['status'] ?? '';
        if (in_array($novo_status, ['pendente', 'aprovada', 'rejeitada'])) {
            $sql = "UPDATE adocoes SET status = '$novo_status' WHERE id = $pedido_id";
            if (mysqli_query($mysqli, $sql)) {
                if ($novo_status == 'aprovada') {
                    $sql_animal = "UPDATE animais SET status = 'adotado' WHERE id = (SELECT animal_id FROM adocoes WHERE id = $pedido_id)";
                    mysqli_query($mysqli, $sql_animal);
                } elseif ($novo_status == 'rejeitada') {
                    $sql_animal = "UPDATE animais SET status = 'disponivel' WHERE id = (SELECT animal_id FROM adocoes WHERE id = $pedido_id)";
                    mysqli_query($mysqli, $sql_animal);
                }
                $mensagem = "✅ Status atualizado com sucesso!";
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
    if (!$resultado || mysqli_num_rows($resultado) === 0) {
        $_SESSION['mensagem_erro'] = "Pedido não encontrado.";
        header('Location: listar.php');
        exit();
    }
    $pedido = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    
    <title>Detalhes do Pedido #<?php echo $pedido['id']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>
    <nav class="navbar admin-navbar">
        <div class="logo"><i class="fa-solid fa-file-lines"></i> Pedido #<?php echo $pedido['id']; ?></div>
        <div class="nav-actions">
            <a href="listar.php" class="btn-admin back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
        </div>
    </nav>

    <div class="admin-container" style="max-width: 800px;">
        
        <?php if ($mensagem): ?>
            <div class="alert-error" style="background-color: <?php echo strpos($mensagem, '✅') ? '#D1FAE5' : '#FEE2E2'; ?>; color: <?php echo strpos($mensagem, '✅') ? '#065F46' : '#991B1B'; ?>;">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3>Dados do Adotante</h3>
            
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($pedido['nome_adotante']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($pedido['email_adotante']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($pedido['telefone_adotante'] ?? 'Não informado'); ?></p>
            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
        </div>

        <div class="form-card" style="margin-top:20px;">
            <h3>Animal Solicitado</h3>
            <p><strong>Pet:</strong> <a href="../animais/editar.php?id=<?php echo $pedido['animal_id']; ?>"><?php echo htmlspecialchars($pedido['nome_animal']); ?></a></p>
            <p><strong>Espécie:</strong> <?php echo htmlspecialchars($pedido['especie']); ?></p>
        </div>

        <div class="form-card" style="margin-top:20px;">
            <h3>Motivação</h3>
            <p><?php echo nl2br(htmlspecialchars($pedido['motivo_adocao'] ?? 'Não informado')); ?></p>
        </div>

        <div class="form-card" style="margin-top: 20px;">
            <h3>Gerenciar Status</h3>
            <p><strong>Status Atual:</strong> <?php echo ucfirst(htmlspecialchars($pedido['status'])); ?></p>
            
            <form method="POST" action="visualizar.php?id=<?php echo $pedido['id']; ?>" style="margin-top: 15px;">
                <div class="form-group">
                    <label>Alterar para:</label>
                    <select name="status" class="form-control" style="max-width: 200px;">
                        <option value="pendente" <?php echo ($pedido['status'] == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                        <option value="aprovada" <?php echo ($pedido['status'] == 'aprovada' ? 'selected' : ''); ?>>Aprovada</option>
                        <option value="rejeitada" <?php echo ($pedido['status'] == 'rejeitada' ? 'selected' : ''); ?>>Rejeitada</option>
                    </select>
                </div>
                <button type="submit" class="btn-login" style="width: auto; padding: 10px 25px;">Atualizar</button>
            </form>
        </div>
    </div>

</body>
</html>