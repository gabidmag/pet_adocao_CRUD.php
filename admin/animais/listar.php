<?php
    require_once '../../verifica-login.php'; 
    require_once '../../conexao.php'; 

    $animais = [];
    $erro = '';

    try {
    $sql = "SELECT id, nome, especie, raca, idade, status, data_cadastro FROM animais ORDER BY data_cadastro DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $animais = $stmt->fetchAll();

    } catch (PDOException $e) {
        $erro = "❌ Erro ao carregar a lista de animais: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Animais - Área Administrativa</title>
    <link rel="stylesheet" href="../../public/css/style.css"> 
    <style>
        /* Estilos básicos para a tabela, se não estiverem no style.css */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-adotado { color: red; font-weight: bold; }
        .status-disponivel { color: green; }
    </style>
</head>
<body>
    <header>
        <h1>Painel Administrativo</h1>
        <nav>
            <a href="../index.php">Início</a> |
            <a href="criar.php">Cadastrar Novo Animal</a> |
            <a href="../../logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <h2>Lista de Animais Cadastrados</h2>

        <?php if ($erro): ?>
            <div class='alerta erro'><?php echo $erro; ?></div>
        <?php endif; ?>

        <?php if (count($animais) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Raça</th>
                        <th>Idade</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($animais as $animal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($animal->id); ?></td>
                            <td><?php echo htmlspecialchars($animal->nome); ?></td>
                            <td><?php echo htmlspecialchars($animal->especie); ?></td>
                            <td><?php echo htmlspecialchars($animal->raca); ?></td>
                            <td><?php echo htmlspecialchars($animal->idade); ?></td>
                            <td class="<?php echo ($animal->status == 'adotado' ? 'status-adotado' : 'status-disponivel'); ?>">
                                <?php echo ucfirst(htmlspecialchars($animal->status)); ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($animal->data_cadastro)); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $animal->id; ?>">Editar</a> |
                                <a href="deletar.php?id=<?php echo $animal->id; ?>" onclick="return confirm('Tem certeza que deseja deletar este animal?');">Deletar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum animal cadastrado ainda. <a href="criar.php">Cadastre um novo</a>.</p>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pet Adoção CRUD</p>
    </footer>
</body>
</html>