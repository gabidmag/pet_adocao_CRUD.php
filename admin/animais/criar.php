<?php
session_start();
// Futuramente se precisar podemos descomentar a linha abaixo para proteger a pagina
// require_once '../../verifica-login.php'; 

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    require_once '../../public/conexao.php';

    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $especie = filter_input(INPUT_POST, 'especie', FILTER_SANITIZE_STRING);
    $raca = filter_input(INPUT_POST, 'raca', FILTER_SANITIZE_STRING);
    $idade_anos = filter_input(INPUT_POST, 'idade_anos', FILTER_VALIDATE_INT);
    $idade_meses = filter_input(INPUT_POST, 'idade_meses', FILTER_VALIDATE_INT);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
    $porte = filter_input(INPUT_POST, 'porte', FILTER_SANITIZE_STRING);
    $localizacao = filter_input(INPUT_POST, 'localizacao', FILTER_SANITIZE_STRING);
    $historia = filter_input(INPUT_POST, 'historia', FILTER_SANITIZE_STRING);
    $taxa_adocao = filter_input(INPUT_POST, 'taxa_adocao', FILTER_VALIDATE_FLOAT);
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    
    $foto_path = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $diretorio_uploads = '../../public/uploads/';
        
        if (!is_dir($diretorio_uploads)) {
            mkdir($diretorio_uploads, 0777, true);
        }

        $nome_arquivo_original = basename($_FILES["foto"]["name"]);
        $extensao = strtolower(pathinfo($nome_arquivo_original, PATHINFO_EXTENSION));
        $nome_arquivo_unico = uniqid() . '_' . time() . '.' . $extensao;
        $caminho_completo = $diretorio_uploads . $nome_arquivo_unico;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($extensao, $allowed_types) && $_FILES['foto']['size'] < 5 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
                $foto_path = 'uploads/' . $nome_arquivo_unico;
            } else {
                $mensagem = "Erro crítico ao mover o arquivo para o destino.";
                $tipo_mensagem = 'erro';
            }
        } else {
            $mensagem = "Arquivo inválido! Apenas JPG, PNG, GIF e tamanho máximo de 5MB são permitidos.";
            $tipo_mensagem = 'erro';
        }
    }

    if (empty($mensagem)) {
        try {
            $sql = "INSERT INTO animais (nome, especie, raca, idade_anos, idade_meses, genero, porte, localizacao, historia, taxa_adocao, foto_path, destaque) 
                    VALUES (:nome, :especie, :raca, :idade_anos, :idade_meses, :genero, :porte, :localizacao, :historia, :taxa_adocao, :foto_path, :destaque)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':nome' => $nome,
                ':especie' => $especie,
                ':raca' => $raca,
                ':idade_anos' => $idade_anos,
                ':idade_meses' => $idade_meses,
                ':genero' => $genero,
                ':porte' => $porte,
                ':localizacao' => $localizacao,
                ':historia' => $historia,
                ':taxa_adocao' => $taxa_adocao,
                ':foto_path' => $foto_path,
                ':destaque' => $destaque
            ]);

            $mensagem = "Animal '" . htmlspecialchars($nome) . "' cadastrado com sucesso!";
            $tipo_mensagem = 'sucesso';
            
        } catch (PDOException $e) {
            $mensagem = "Erro ao cadastrar animal no banco de dados: " . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Cadastrar Novo Animal</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #212529; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #343a40; margin-bottom: 30px; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: .5rem; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: .75rem; border: 1px solid #ced4da; border-radius: .25rem; box-sizing: border-box; transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #80bdff; outline: 0; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .form-group-checkbox { display: flex; align-items: center; gap: 10px; }
        .form-group-checkbox input { width: auto; }
        .btn { display: inline-block; width: 100%; padding: .75rem; background-color: #28a745; color: white; border: none; border-radius: .25rem; cursor: pointer; font-size: 1rem; font-weight: 600; text-align: center; text-decoration: none; }
        .btn:hover { background-color: #218838; }
        .message { padding: 1rem; margin-bottom: 1.5rem; border-radius: .25rem; color: #fff; text-align: center; font-weight: 500; }
        .message.sucesso { background-color: #28a745; }
        .message.erro { background-color: #dc3545; }
        .form-row { display: flex; gap: 20px; }
        .form-row .form-group { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastrar Novo Animal</h1>

        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form action="criar.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome">Nome do Animal</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="especie">Espécie</label>
                    <select id="especie" name="especie" required>
                        <option value="Cachorro">Cachorro</option>
                        <option value="Gato">Gato</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="raca">Raça</label>
                    <input type="text" id="raca" name="raca">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="idade_anos">Idade (Anos)</label>
                    <input type="number" id="idade_anos" name="idade_anos" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="idade_meses">Idade (Meses)</label>
                    <input type="number" id="idade_meses" name="idade_meses" min="0" max="11" value="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="genero">Gênero</label>
                    <select id="genero" name="genero" required>
                        <option value="Macho">Macho</option>
                        <option value="Fêmea">Fêmea</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="porte">Porte</label>
                    <select id="porte" name="porte" required>
                        <option value="Pequeno">Pequeno</option>
                        <option value="Médio">Médio</option>
                        <option value="Grande">Grande</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="localizacao">Localização (Cidade, UF)</label>
                <input type="text" id="localizacao" name="localizacao">
            </div>
            <div class="form-group">
                <label for="historia">História / Descrição do Animal</label>
                <textarea id="historia" name="historia"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="taxa_adocao">Taxa de Adoção (R$)</label>
                    <input type="number" step="0.01" id="taxa_adocao" name="taxa_adocao" value="0.00">
                </div>
                <div class="form-group">
                    <label for="foto">Foto do Animal (Max 5MB)</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg, image/png, image/gif">
                </div>
            </div>
            <div class="form-group form-group-checkbox">
                <input type="checkbox" id="destaque" name="destaque" value="1">
                <label for="destaque">Marcar como Destaque na página inicial</label>
            </div>
            <button type="submit" class="btn">Cadastrar Animal</button>
        </form>
    </div>
</body>
</html>