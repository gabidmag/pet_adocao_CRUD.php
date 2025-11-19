<?php
session_start();
require_once 'conexao.php';

$pet_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT);
$feedback = '';
$animal = null;

if ($pet_id) {
    try {
        $stmt = $pdo->prepare('SELECT id, nome, raca, especie, idade_anos FROM animais WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $pet_id, PDO::PARAM_INT);
        $stmt->execute();
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
    }
}

// Não enviaremos para nenhum lugar, apenas simulamos um ok na interface.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = 'Recebemos o seu pedido! Logo alguém da ONG entrará em contato.';
}

require_once 'templates/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="animais.php">Pets</a> / Pedido de adoção</p>
        <h1>Formulário de adoção</h1>
        <p>Preencha seus dados para que a equipe entre em contato.</p>
    </div>
</section>

<section class="adoption-form">
    <div class="container">
        <?php if ($feedback): ?>
            <div class="alert success"><?php echo htmlspecialchars($feedback); ?></div>
        <?php endif; ?>

        <?php if ($animal): ?>
            <div class="adoption-form__summary">
                <h2><?php echo htmlspecialchars($animal['nome']); ?></h2>
                <p><?php echo htmlspecialchars($animal['raca']); ?> • <?php echo htmlspecialchars($animal['especie']); ?> • <?php echo htmlspecialchars($animal['idade_anos']); ?> ano(s)</p>
            </div>
        <?php endif; ?>

        <form method="POST" action="adotar.php" class="form-grid">
            <input type="hidden" name="pet_id" value="<?php echo $animal['id'] ?? ''; ?>">

            <label>
                Nome completo
                <input type="text" name="nome" required placeholder="Seu nome completo">
            </label>

            <label>
                E-mail
                <input type="email" name="email" required placeholder="seuemail@email.com">
            </label>

            <label>
                Telefone
                <input type="tel" name="telefone" placeholder="(00) 00000-0000">
            </label>

            <label>
                Cidade/Estado
                <input type="text" name="cidade" placeholder="Ex: Recife / PE">
            </label>

            <label class="full">
                Conte um pouco sobre você
                <textarea name="mensagem" rows="4" placeholder="Fale sobre o ambiente, rotina e por que quer adotar."></textarea>
            </label>

            <div class="form-actions full">
                <button type="submit" class="btn btn-primary">Enviar pedido</button>
                <a href="detalhe-animal.php?id=<?php echo $animal['id'] ?? 0; ?>" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once 'templates/footer.php'; ?>
