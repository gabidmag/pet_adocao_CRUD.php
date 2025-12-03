<?php
    require_once '../../conexao.php';
    require_once '../../verifica-login.php';
    require_login('../../login.php'); 

    $mensagem = '';
    $upload_dir = '../../public/uploads/'; // Ajustei para a pasta public correta

    // Cria a pasta se não existir
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Pega dados do formulário
        $nome = $_POST['nome'] ?? '';
        $especie = $_POST['especie'] ?? '';
        $raca = $_POST['raca'] ?? '';
        // ATENÇÃO: Se seu banco usa 'idade_anos', mude aqui embaixo e no SQL
        $idade = $_POST['idade'] ?? 0; 
        $descricao = $_POST['descricao'] ?? '';
        $status = 'disponivel';
        
        $foto_nome = null;
        
        // Upload da foto
        if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto_nome = uniqid() . '.' . $extensao; // Nome único
            $caminho_completo = $upload_dir . $foto_nome;
            
            // Tipos permitidos
            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($_FILES['foto']['type'], $tipos_permitidos) && $_FILES['foto']['size'] < 5000000) {
                if(move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                    // Upload ok
                } else {
                    $mensagem = "❌ Erro ao mover o arquivo para a pasta.";
                }
            } else {
                $mensagem = "❌ Foto inválida. Apenas imagens e máximo 5MB.";
            }
        }
        
        // Valida e insere
        if (empty($nome) || empty($especie)) {
            $mensagem = "❌ Preencha Nome e Espécie.";
        } elseif (empty($mensagem) || strpos($mensagem, '❌') === false) {
            
            // AJUSTE O SQL SE NECESSÁRIO: 
            // Verifique se no seu banco a coluna é 'idade' ou 'idade_anos'. 
            // Vou usar 'idade_anos' pois foi o que vimos na sua tabela antes.
            // Se der erro, troque de volta para 'idade'.
            $sql = "INSERT INTO animais (nome, especie, raca, idade_anos, descricao, foto, status) 
                    VALUES ('$nome', '$especie', '$raca', $idade, '$descricao', '$foto_nome', '$status')";
            
            if (mysqli_query($mysqli, $sql)) {
                $mensagem = "✅ Animal cadastrado com sucesso!";
            } else {
                $mensagem = "❌ Erro no banco: " . mysqli_error($mysqli);
                // Apaga foto se deu erro no banco
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
    <title>Novo Animal - Admin</title>
    <!-- Ícones e CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css"> 
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo"><i class="fa-solid fa-paw"></i> Cadastro</div>
        <div class="nav-actions">
            <a href="../index.php">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="admin-container" style="max-width: 700px;">
        
        <div class="admin-header">
            <h2>Cadastrar Novo Pet</h2>
            <a href="listar.php" class="btn-cancel" style="font-size:0.9rem;">Ver Lista</a>
        </div>

        <!-- Exibe Mensagens -->
        <?php if ($mensagem): ?>
            <div class="alert-error" style="background-color: <?php echo strpos($mensagem, '✅') !== false ? '#D1FAE5' : '#FEE2E2'; ?>; color: <?php echo strpos($mensagem, '✅') !== false ? '#065F46' : '#991B1B'; ?>;">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <!-- Formulário Estilizado -->
        <form method="POST" enctype="multipart/form-data" action="criar.php" class="form-card">
            
            <div class="form-group">
                <label for="nome">Nome do Pet</label>
                <input type="text" id="nome" name="nome" class="form-control" required placeholder="Ex: Rex, Luna...">
            </div>

            <div class="form-group">
                <label for="especie">Espécie</label>
                <div class="input-wrapper">
                    <select id="especie" name="especie" class="form-control" required>
                        <option value="">Selecione...</option>
                        <option value="Cachorro">Cachorro</option>
                        <option value="Gato">Gato</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
            </div>

            <!-- Grupo lado a lado -->
            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label for="raca">Raça</label>
                    <input type="text" id="raca" name="raca" class="form-control" placeholder="Ex: Vira-lata">
                </div>

                <div class="form-group" style="width: 150px;">
                    <label for="idade">Idade (anos)</label>
                    <input type="number" id="idade" name="idade" class="form-control" min="0" max="30" value="0">
                </div>
            </div>
            
            <div class="form-group">
                <label for="descricao">História / Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="5" placeholder="Conte sobre a personalidade do pet..."></textarea>
            </div>

            <div class="form-group">
                <label for="foto">Foto do Animal</label>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                <p style="font-size: 0.8rem; color: #888; margin-top: 5px;">Formatos: JPG, PNG. Máx: 5MB.</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-login" style="width: auto; padding: 12px 30px;">
                    <i class="fa-solid fa-save"></i> Salvar Cadastro
                </button>
            </div>

        </form>
    </div>

</body>
</html>