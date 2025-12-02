<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $mensagem = '';
    $upload_dir = '../../uploads/'; 

    // Cria a pasta se não existir
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Pega dados do formulário
        $nome = $_POST['nome'] ?? '';
        $especie = $_POST['especie'] ?? '';
        $raca = $_POST['raca'] ?? '';
        $idade = $_POST['idade'] ?? 0;
        $descricao = $_POST['descricao'] ?? '';
        $status = 'disponivel';
        
        $foto_nome = null;
        
        // Upload da foto
        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nome = uniqid() . '.' . $extensao;
            $caminho_completo = $upload_dir . $foto_nome;
            
            // Tipos permitidos
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (in_array($_FILES['foto']['type'], $tipos_permitidos) && $_FILES['foto']['size'] < 5000000) {
                move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo);
            } else {
                $mensagem = "❌ Foto inválida. Apenas JPEG, PNG, GIF e máximo 5MB.";
            }
        }
        
        // Valida e insere
        if (empty($nome) || empty($especie)) {
            $mensagem = "❌ Preencha Nome e Espécie.";
        } elseif (empty($mensagem)) {
            
            // Query de inserção
            $sql = "INSERT INTO animais (nome, especie, raca, idade, descricao, foto, status) 
                    VALUES ('$nome', '$especie', '$raca', $idade, '$descricao', '$foto_nome', '$status')";
            
            if (mysqli_query($mysqli, $sql)) {
                $mensagem = "✅ Animal $nome cadastrado com sucesso!";
            } else {
                $mensagem = "❌ Erro ao cadastrar: " . mysqli_error($mysqli);
                
                // Apaga foto se deu erro
                if ($foto_nome && file_exists($upload_dir . $foto_nome)) {
                    unlink($upload_dir . $foto_nome);
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