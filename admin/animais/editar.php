<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $mensagem = '';
    $animal = null;
    $upload_dir = '../../uploads/'; 
    $animal_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // 1. Lógica de Busca: Carrega os dados do animal
    if (!$animal_id) {
        header('Location: listar.php');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, especie, raca, idade, descricao, foto, status FROM animais WHERE id = :id");
        $stmt->bindParam(':id', $animal_id);
        $stmt->execute();
        
        $animal = $stmt->fetch();

        if (!$animal) {
            $mensagem = "❌ Animal não encontrado.";
            header('Refresh: 3; URL=listar.php'); 
        }

    } catch (PDOException $e) {
        $mensagem = "❌ Erro ao buscar animal: " . $e->getMessage();
    }

    // 2. Lógica de Edição: Processa o POST para atualizar o animal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $animal) {
        
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $especie = filter_input(INPUT_POST, 'especie', FILTER_SANITIZE_STRING);
        $raca = filter_input(INPUT_POST, 'raca', FILTER_SANITIZE_STRING);
        $idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $foto_atual = $animal->foto; 

        // A. Lógica para NOVO upload de imagem
        if (isset($_FILES['foto_nova']) && $_FILES['foto_nova']['error'] === UPLOAD_ERR_OK) {
            
            $extensao = pathinfo($_FILES['foto_nova']['name'], PATHINFO_EXTENSION);
            $novo_foto_nome = uniqid() . '.' . $extensao;
            $caminho_completo = $upload_dir . $novo_foto_nome;

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['foto_nova']['type'], $allowed_types) && $_FILES['foto_nova']['size'] < 5000000) {
                
                if (move_uploaded_file($_FILES['foto_nova']['tmp_name'], $caminho_completo)) {
                    $foto_atual = $novo_foto_nome;

                    if ($animal->foto && file_exists($upload_dir . $animal->foto)) {
                        unlink($upload_dir . $animal->foto);
                    }

                } else {
                    $mensagem = "Erro ao fazer upload da foto. Verifique as permissões.";
                }
            } else {
                $mensagem = "Arquivo inválido para foto.";
            }
        }
        
        // B. Validação e Update no DB
        if (empty($nome) || empty($especie) || $idade === false) {
            $mensagem = "❌ Por favor, preencha os campos obrigatórios.";
        } else if (empty($mensagem)) {
            try {
                $sql = "UPDATE animais SET nome = :nome, especie = :especie, raca = :raca, idade = :idade, descricao = :descricao, foto = :foto, status = :status WHERE id = :id";
                
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':especie', $especie);
                $stmt->bindParam(':raca', $raca);
                $stmt->bindParam(':idade', $idade);
                $stmt->bindParam(':descricao', $descricao);
                $stmt->bindParam(':foto', $foto_atual);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $animal_id);
                
                $stmt->execute();
                
                $mensagem = "✅ Animal **" . htmlspecialchars($nome) . "** atualizado com sucesso!";

                $stmt = $pdo->prepare("SELECT id, nome, especie, raca, idade, descricao, foto, status FROM animais WHERE id = :id");
                $stmt->bindParam(':id', $animal_id);
                $stmt->execute();
                $animal = $stmt->fetch();

            } catch (PDOException $e) {
                $mensagem = "❌ Erro ao atualizar animal: " . $e->getMessage();
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