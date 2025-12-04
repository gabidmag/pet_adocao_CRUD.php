<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $pedidos = [];
    $erro = '';

    // SQL que busca os PEDIDOS DE ADOÇÃO (e não os animais)
    $sql = "SELECT 
                a.id, a.nome_adotante, a.email_adotante, a.data_pedido, a.status,
                p.nome AS nome_animal, p.id AS animal_id
            FROM adocoes a
            JOIN animais p ON a.animal_id = p.id
            ORDER BY a.data_pedido DESC";

    $resultado = mysqli_query($mysqli, $sql);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $pedidos[] = $row;
        }
    } else {
        $erro = "Erro ao buscar pedidos: " . mysqli_error($mysqli);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedidos de Adoção - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>

    <!-- NAV ADMIN PADRÃO -->
    <nav class="navbar admin-navbar">
        <div class="logo"><i class="fa-solid fa-envelope-open-text"></i> Pedidos de Adoção</div>
        <div class="nav-actions">
            <a href="../index.php" class="btn-admin back">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </nav>

    <div class="admin-container">
        
        <div class="admin-header">
            <div>
                <h2>Solicitações Recebidas</h2>
                <p style="color:var(--text-light); font-size: 0.9rem;">Analise os pedidos para aprovar ou rejeitar.</p>
            </div>
        </div>
        
        <?php if ($erro): ?>
            <div class="alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Adotante</th>
                        <th>Contato</th>
                        <th>Interesse no Pet</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-light);">
                                Nenhum pedido de adoção encontrado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo $pedido['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($pedido['nome_adotante']); ?></strong></td>
                                <td style="font-size: 0.9rem;"><?php echo htmlspecialchars($pedido['email_adotante']); ?></td>
                                <td>
                                    <a href="../animais/editar.php?id=<?php echo $pedido['animal_id']; ?>">
                                        <?php echo htmlspecialchars($pedido['nome_animal']); ?>
                                    </a>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
                                <td>
                                    <?php 
                                        $status = $pedido['status'] ?? 'pendente';
                                        $classe = '';
                                        if($status == 'aprovada') $classe = 'disponivel';
                                        elseif($status == 'rejeitada') $classe = 'indisponivel';
                                        else $classe = 'adotado';
                                    ?>
                                    <span class="status-badge <?php echo $classe; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td class="action-links">
                                    <a href="visualizar.php?id=<?php echo $pedido['id']; ?>" class="edit-btn" title="Ver Detalhes"><i class="fa-solid fa-eye"></i></a>
                                    <a href="deletar.php?id=<?php echo $pedido['id']; ?>" class="delete-btn" title="Excluir" onclick="return confirm('Excluir este pedido?');"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>