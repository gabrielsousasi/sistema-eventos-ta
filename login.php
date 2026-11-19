<?php
require_once 'includes/funcoes.php';

if (usuario_logado()) {
    header('Location: inicio.php');
    exit;
}

$email = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if (!csrf_valido()) {
        $erro = 'A página expirou. Tente entrar novamente.';
    } else {
        $encontrado = null;
        foreach (ler_json('usuarios.json') as $usuario) {
            if ($usuario['email'] === $email) {
                $encontrado = $usuario;
                break;
            }
        }

        if ($encontrado && password_verify($senha, $encontrado['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $encontrado['id'];
            $_SESSION['usuario_nome'] = $encontrado['nome'];
            header('Location: inicio.php');
            exit;
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}

$titulo = 'Entrar';
require 'includes/cabecalho.php';
?>
<section class="cartao-form">
    <img src="img/logo-republica.png" alt="República To Atoa, UFV-CRP" class="logo-auth">
    <h1>Entrar</h1>
    <p class="apoio">Acesse sua conta para ver e agendar eventos.</p>

    <?php if ($erro): ?>
        <div class="alerta alerta-erro" role="alert"><?= e($erro) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" data-validar novalidate>
        <?= csrf_campo() ?>
        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
        </div>
        <div class="campo">
            <label for="senha">Senha</label>
            <div class="campo-senha">
                <input type="password" id="senha" name="senha" autocomplete="current-password" required>
                <button type="button" class="btn-olho" data-alvo="senha" aria-label="Mostrar senha">Mostrar</button>
            </div>
        </div>
        <button class="botao" type="submit">Entrar</button>
    </form>

    <p class="troca">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</section>
<?php require 'includes/rodape.php'; ?>
