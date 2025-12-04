<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $mensagem = '';
    $animal_id = $_GET['id'] ?? 0;
    if (!$animal_id) { header('Location: listar.php'); exit(); }

    
    $upload_dir = '../../public/uploads/'; 
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $especie = $_POST['especie'] ?? '';
        $raca = $_POST['raca'] ?? '';
        
        $idade_anos = $_POST['idade_anos'] ?? 0;
        $descricao = $_POST['descricao'] ?? '';
        $status = $_POST['status'] ?? 'disponivel';
        
        
        $sql_foto = "SELECT foto FROM animais WHERE id = $animal_id";
        $result_foto = mysqli_query($mysqli, $sql_foto);
        $foto_atual = mysqli_fetch_assoc($result_foto)['foto'];
        
        
        if (!empty($_FILES['foto_nova']['name'])) {
            $novo_foto_nome = uniqid() . '-' . basename($_FILES['foto_nova']['name']);
            if (move_uploaded_file($_FILES['foto_nova']['tmp_name'], $upload_dir . $novo_foto_nome)) {
                if (!empty($foto_atual) && file_exists($upload_dir . $foto_atual)) {
                    unlink($upload_dir . $foto_atual); 
                }
                $foto_atual = $novo_foto_nome; 
            }
        }
        
        
        $sql_update = "UPDATE animais SET nome = ?, especie = ?, raca = ?, idade_anos = ?, descricao = ?, foto = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($mysqli, $sql_update);
        mysqli_stmt_bind_param($stmt, 'sssisssi', $nome, $especie, $raca, $idade_anos, $descricao, $foto_atual, $status, $animal_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensagem = "✅ Animal atualizado com sucesso!";
        } else {
            $mensagem = "❌ Erro ao atualizar.";
        }
    }

    
    $sql_busca = "SELECT * FROM animais WHERE id = $animal_id";
    $resultado = mysqli_query($mysqli, $sql_busca);
    if (!$resultado || mysqli_num_rows($resultado) === 0) {
        $_SESSION['mensagem_erro'] = "Animal não encontrado.";
        header('Location: listar.php'); exit();
    }
    $animal = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal: <?php echo htmlspecialchars($animal['nome']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>

    <nav class="navbar admin-navbar">
        <div class="logo"><i class="fa-solid fa-pen-to-square"></i> Editar Pet</div>
        <div class="nav-actions">
            <a href="listar.php" class="btn-admin back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
        </div>
    </nav>

    <div class="admin-container" style="max-width: 700px;">
        
        <div class="admin-header">
            <h2>Editando: <?php echo htmlspecialchars($animal['nome']); ?></h2>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert-error" style="background-color:<?php echo strpos($mensagem,'✅')?'#D1FAE5':'#FEE2E2';?>; color:<?php echo strpos($mensagem,'✅')?'#065F46':'#991B1B';?>;"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" action="editar.php?id=<?php echo $animal_id; ?>" class="form-card">
            
            <div class="form-group">
                <label>Nome do Pet</label>
                
                <input type="text" name="nome" class="form-control" required value="<?php echo htmlspecialchars($animal['nome']); ?>">
            </div>

            <div class="form-group">
                <label>Espécie</label>
                <select name="especie" class="form-control">
                    <option value="Cachorro" <?php echo ($animal['especie'] == 'Cachorro') ? 'selected' : ''; ?>>Cachorro</option>
                    <option value="Gato" <?php echo ($animal['especie'] == 'Gato') ? 'selected' : ''; ?>>Gato</option>
                </select>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Raça</label>
                    <input type="text" name="raca" class="form-control" value="<?php echo htmlspecialchars($animal['raca'] ?? ''); ?>">
                </div>
                <div class="form-group" style="width: 150px;">
                    <label>Idade (anos)</label>
                    
                    <input type="number" name="idade_anos" class="form-control" min="0" value="<?php echo htmlspecialchars($animal['idade_anos'] ?? '0'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao" class="form-control" rows="5"><?php echo htmlspecialchars($animal['descricao'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="disponivel" <?php echo ($animal['status'] == 'disponivel' ? 'selected' : ''); ?>>Disponível</option>
                    <option value="adotado" <?php echo ($animal['status'] == 'adotado' ? 'selected' : ''); ?>>Adotado</option>
                </select>
            </div>

            <div class="form-group">
                <label>Foto Atual</label>
                <?php if (!empty($animal['foto'])): ?>
                    <img src="../../public/uploads/<?php echo basename($animal['foto']); ?>" alt="Foto" style="max-width: 100px; border-radius: 8px; margin-bottom: 10px;">
                <?php else: ?>
                    <p style="color: #888;">Nenhuma foto.</p>
                <?php endif; ?>
                <label>Alterar Foto (opcional)</label>
                
                <input type="file" name="foto_nova" class="form-control">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-login" style="width: auto;">Salvar Alterações</button>
            </div>

        </form>
    </div>

</body>
</html>