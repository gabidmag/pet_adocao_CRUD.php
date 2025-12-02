<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $mensagem = '';
    $animal = null;
    $upload_dir = '../../uploads/'; 

    // Pega o ID do animal
    $animal_id = $_GET['id'] ?? 0;

    // Verifica se tem ID
    if (!$animal_id) {
        header('Location: listar.php');
        exit();
    }

    // Cria a pasta de uploads se não existir
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 1. Busca os dados do animal
    $sql_busca = "SELECT * FROM animais WHERE id = $animal_id";
    $resultado = mysqli_query($mysqli, $sql_busca);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $animal = mysqli_fetch_assoc($resultado);
    } else {
        $mensagem = "❌ Animal não encontrado.";
        header('Refresh: 3; URL=listar.php'); 
    }

    // 2. Processa a edição se for POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $animal) {
        
        // Pega dados do formulário
        $nome = $_POST['nome'] ?? '';
        $especie = $_POST['especie'] ?? '';
        $raca = $_POST['raca'] ?? '';
        $idade = $_POST['idade'] ?? 0;
        $descricao = $_POST['descricao'] ?? '';
        $status = $_POST['status'] ?? 'disponivel';
        
        $foto_atual = $animal['foto']; // Mantém a foto atual
        
        // Se enviou nova foto
        if (!empty($_FILES['foto_nova']['name']) && $_FILES['foto_nova']['error'] === UPLOAD_ERR_OK) {
            
            $extensao = pathinfo($_FILES['foto_nova']['name'], PATHINFO_EXTENSION);
            $novo_foto_nome = uniqid() . '.' . $extensao;
            $caminho_completo = $upload_dir . $novo_foto_nome;
            
            // Tipos permitidos
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (in_array($_FILES['foto_nova']['type'], $tipos_permitidos) && $_FILES['foto_nova']['size'] < 5000000) {
                
                // Faz upload da nova foto
                if (move_uploaded_file($_FILES['foto_nova']['tmp_name'], $caminho_completo)) {
                    
                    // Apaga a foto antiga se existir
                    if (!empty($animal['foto']) && file_exists($upload_dir . $animal['foto'])) {
                        unlink($upload_dir . $animal['foto']);
                    }
                    
                    $foto_atual = $novo_foto_nome;
                }
            }
        }
        
        // Valida e atualiza
        if (empty($nome) || empty($especie)) {
            $mensagem = "❌ Preencha Nome e Espécie.";
        } else {
            
            $sql_update = "UPDATE animais 
                        SET nome = '$nome', especie = '$especie', raca = '$raca', 
                            idade = $idade, descricao = '$descricao', 
                            foto = '$foto_atual', status = '$status' 
                        WHERE id = $animal_id";
            
            if (mysqli_query($mysqli, $sql_update)) {
                $mensagem = "✅ Animal $nome atualizado com sucesso!";
                
                // Recarrega os dados atualizados
                $sql_busca = "SELECT * FROM animais WHERE id = $animal_id";
                $resultado = mysqli_query($mysqli, $sql_busca);
                if ($resultado) {
                    $animal = mysqli_fetch_assoc($resultado);
                }
            } else {
                $mensagem = "❌ Erro ao atualizar: " . mysqli_error($mysqli);
            }
        }
    }

    if ($animal):
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal - Área Administrativa</title>
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="../index.php">Início</a> |
            <a href="listar.php">Listar Animais</a> |
            <a href="../../logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Editar Animal: <?php echo htmlspecialchars($animal->nome); ?></h2>

        <?php 
        if ($mensagem) {
            $classe_alerta = strpos($mensagem, '✅') !== false ? 'sucesso' : 'erro';
            echo "<div class='alerta {$classe_alerta}'>{$mensagem}</div>";
        }
        ?>

        <form method="POST" enctype="multipart/form-data" action="editar.php?id=<?php echo $animal->id; ?>">
            
            <fieldset>
                <legend>Dados do Animal</legend>

                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required maxlength="100" value="<?php echo htmlspecialchars($animal->nome); ?>">
                </div>

                <div>
                    <label for="especie">Espécie:</label>
                    <select id="especie" name="especie" required>
                        <option value="Cachorro" <?php echo ($animal->especie == 'Cachorro' ? 'selected' : ''); ?>>Cachorro</option>
                        <option value="Gato" <?php echo ($animal->especie == 'Gato' ? 'selected' : ''); ?>>Gato</option>
                        <option value="Outro" <?php echo ($animal->especie == 'Outro' ? 'selected' : ''); ?>>Outro</option>
                    </select>
                </div>

                <div>
                    <label for="raca">Raça:</label>
                    <input type="text" id="raca" name="raca" maxlength="100" value="<?php echo htmlspecialchars($animal->raca); ?>">
                </div>

                <div>
                    <label for="idade">Idade (anos):</label>
                    <input type="number" id="idade" name="idade" required min="0" max="30" value="<?php echo htmlspecialchars($animal->idade); ?>">
                </div>

                <div>
                    <label for="status">Status de Adoção:</label>
                    <select id="status" name="status" required>
                        <option value="disponivel" <?php echo ($animal->status == 'disponivel' ? 'selected' : ''); ?>>Disponível</option>
                        <option value="processo" <?php echo ($animal->status == 'processo' ? 'selected' : ''); ?>>Em Processo</option>
                        <option value="adotado" <?php echo ($animal->status == 'adotado' ? 'selected' : ''); ?>>Adotado</option>
                    </select>
                </div>
                
                <div>
                    <label for="descricao">Descrição / Histórico:</label>
                    <textarea id="descricao" name="descricao" rows="5"><?php echo htmlspecialchars($animal->descricao); ?></textarea>
                </div>

                <div>
                    <label>Foto Atual:</label>
                    <?php if ($animal->foto): ?>
                        <img src="../../uploads/<?php echo htmlspecialchars($animal->foto); ?>" alt="Foto de <?php echo htmlspecialchars($animal->nome); ?>" style="max-width: 150px; display: block; margin-bottom: 10px;">
                    <?php else: ?>
                        <p>Nenhuma foto cadastrada.</p>
                    <?php endif; ?>
                    
                    <label for="foto_nova">Alterar Foto (Max 5MB):</label>
                    <input type="file" id="foto_nova" name="foto_nova" accept="image/*">
                </div>

            </fieldset>

            <button type="submit">Salvar Alterações</button>
            <a href="listar.php" class="botao-cancelar">Voltar para a Lista</a>

        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>
<?php endif; ?>