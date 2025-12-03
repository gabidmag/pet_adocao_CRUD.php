<?php
// Caminhos relativos para sair da pasta admin/animais/
require_once '../../conexao.php';
require_once '../../verifica-login.php';

// Busca os animais ordenados pelo ID decrescente (mais novos primeiro)
$sql = "SELECT * FROM animais ORDER BY id DESC";
$resultado = mysqli_query($mysqli, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Animais</title>
    
    <!-- Ícones FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SEU CSS MODERNO -->
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>

    <!-- Navbar do Admin -->
    <nav class="navbar">
        <div class="logo"><i class="fa-solid fa-shield-dog"></i> Painel Admin</div>
        <div class="nav-actions">
            <a href="../index.php" style="text-decoration:none; color:var(--text-dark); font-weight:500;">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </nav>

    <div class="admin-container">
        
        <!-- Cabeçalho da Tabela -->
        <div class="admin-header">
            <div>
                <h2 style="font-size: 1.5rem; color: var(--secondary-color);">Animais Cadastrados</h2>
                <p style="color: var(--text-light); font-size: 0.9rem;">Gerencie os pets disponíveis para adoção.</p>
            </div>
            <a href="criar.php" class="btn-add">
                <i class="fa-solid fa-plus"></i> Novo Animal
            </a>
        </div>

        <!-- Tabela Estilizada -->
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="80">Foto</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Raça</th>
                        <th>Idade</th>
                        <th>Status</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                        <?php
                            // Lógica para exibir a imagem corretamente
                            $foto = $row['foto'];
                            
                            // Se não tem foto, usa placeholder
                            if(empty($foto)) {
                                $imgSrc = 'https://placehold.co/100x100?text=Sem+Foto';
                            } 
                            // Se já for um link (http), usa ele
                            elseif (strpos($foto, 'http') === 0) {
                                $imgSrc = $foto;
                            } 
                            // Se for arquivo local, aponta para a pasta public/uploads
                            else {
                                $imgSrc = '../../public/uploads/' . basename($foto);
                            }
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo $imgSrc; ?>" alt="Pet" class="thumb-img">
                            </td>
                            
                            <td>
                                <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                            </td>
                            
                            <td><?php echo htmlspecialchars($row['especie']); ?></td>
                            
                            <td><?php echo htmlspecialchars($row['raca'] ?? 'SRD'); ?></td>
                            
                            <!-- AQUI ESTAVA O ERRO: Mudamos para idade_anos -->
                            <td>
                                <?php 
                                    $anos = $row['idade_anos'] ?? 0;
                                    echo $anos . ($anos == 1 ? ' ano' : ' anos');
                                ?>
                            </td>

                            <td>
                                <?php 
                                    $status = $row['status'] ?? 'disponivel';
                                    $classe = '';
                                    if($status == 'disponivel') $classe = 'disponivel';
                                    if($status == 'adotado') $classe = 'adotado';
                                ?>
                                <span class="status-badge <?php echo $classe; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            
                            <td class="action-links">
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="edit-btn" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="deletar.php?id=<?php echo $row['id']; ?>" class="delete-btn" title="Excluir" onclick="return confirm('Tem certeza que deseja apagar o registro de <?php echo $row['nome']; ?>?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>