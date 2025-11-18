<?php
    
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $mensagem = '';
    $upload_dir = '../../uploads/'; 

    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // 1. Coleta e sanitiza os dados do formulário
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $especie = filter_input(INPUT_POST, 'especie', FILTER_SANITIZE_STRING);
        $raca = filter_input(INPUT_POST, 'raca', FILTER_SANITIZE_STRING);
        $idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
        $status = 'disponivel'; 

        $foto_nome = null;
        $caminho_completo = null;

        // 2. Lógica para upload de imagem
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nome = uniqid() . '.' . $extensao; // Nome único
            $caminho_completo = $upload_dir . $foto_nome;

            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['foto']['type'], $allowed_types) && $_FILES['foto']['size'] < 5000000) { // Limite de 5MB
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                    $mensagem = "Erro ao fazer upload da foto. Verifique as permissões da pasta 'uploads'.";
                }
            } else {
                $mensagem = "Arquivo inválido. Apenas JPEG, PNG, GIF e tamanho máximo de 5MB.";
            }
        }

        // 3. Validação e Inserção no DB
        if (empty($nome) || empty($especie) || $idade === false) {
            $mensagem = "❌ Por favor, preencha os campos obrigatórios (Nome, Espécie e Idade).";
        } else if (empty($mensagem)) {
            try {
                $sql = "INSERT INTO animais (nome, especie, raca, idade, descricao, foto, status) 
                        VALUES (:nome, :especie, :raca, :idade, :descricao, :foto, :status)";
                
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':especie', $especie);
                $stmt->bindParam(':raca', $raca);
                $stmt->bindParam(':idade', $idade);
                $stmt->bindParam(':descricao', $descricao);
                $stmt->bindParam(':foto', $foto_nome);
                $stmt->bindParam(':status', $status);
                
                $stmt->execute();
                
                $mensagem = "✅ Animal **" . htmlspecialchars($nome) . "** cadastrado com sucesso!";

            } catch (PDOException $e) {
                $mensagem = "❌ Erro ao cadastrar animal: " . $e->getMessage();
                if ($foto_nome && file_exists($caminho_completo)) {
                    unlink($caminho_completo);
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Novo Animal - Área Administrativa</title>
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
        <h2>Cadastrar Novo Animal</h2>

        <?php 
        
        if ($mensagem) {
            
            $classe_alerta = strpos($mensagem, '✅') !== false ? 'sucesso' : 'erro';
            echo "<div class='alerta {$classe_alerta}'>{$mensagem}</div>";
        }
        ?>

        <form method="POST" enctype="multipart/form-data" action="criar.php">
            
            <fieldset>
                <legend>Dados do Animal</legend>

                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required maxlength="100">
                </div>

                <div>
                    <label for="especie">Espécie:</label>
                    <select id="especie" name="especie" required>
                        <option value="">Selecione a Espécie</option>
                        <option value="Cachorro">Cachorro</option>
                        <option value="Gato">Gato</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div>
                    <label for="raca">Raça:</label>
                    <input type="text" id="raca" name="raca" maxlength="100">
                </div>

                <div>
                    <label for="idade">Idade (anos):</label>
                    <input type="number" id="idade" name="idade" required min="0" max="30">
                </div>
                
                <div>
                    <label for="descricao">Descrição / Histórico:</label>
                    <textarea id="descricao" name="descricao" rows="5"></textarea>
                </div>

                <div>
                    <label for="foto">Foto do Animal (Max 5MB):</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>

            </fieldset>

            <button type="submit">Cadastrar Animal</button>
            <a href="listar.php" class="botao-cancelar">Voltar para a Lista</a>

        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>