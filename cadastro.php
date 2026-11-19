<?php
require_once 'includes/funcoes.php';

if (usuario_logado()) {
    header('Location: inicio.php');
    exit;
}

$nome = '';
$email = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirmacao = $_POST['confirmacao'] ?? '';

    if (!csrf_valido()) {
        $erros[] = 'A página expirou. Tente novamente.';
    }
    if ($nome === '' || tamanho_texto($nome) > 100) {
        $erros[] = 'Informe seu nome (até 100 caracteres).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (strlen($senha) < 6) {
        $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmacao) {
        $erros[] = 'A confirmação da senha não confere.';
    }

    $usuarios = ler_json('usuarios.json');

    if (!$erros) {
        foreach ($usuarios as $usuario) {
            if ($usuario['email'] === $email) {
                $erros[] = 'Já existe uma conta com esse e-mail.';
                break;
            }
        }
    }

    if (!$erros) {
        $usuarios[] = [
            'id' => proximo_id($usuarios),
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
        ];
        if (salvar_json('usuarios.json', $usuarios)) {
            mensagem('sucesso', 'Conta criada! Entre com seu e-mail e senha.');
            header('Location: login.php');
            exit;
        }
        $erros[] = 'Não foi possível salvar o cadastro. Verifique as permissões da pasta dados/.';
    }
}

$titulo = 'Criar conta';
require 'includes/cabecalho.php';
?>
<section class="cartao-form">
    <img src="img/logo-republica.png" alt="República To Atoa, UFV-CRP" class="logo-auth">
    <h1>Criar conta</h1>
    <p class="apoio">Leva menos de um minuto.</p>

    <?php foreach ($erros as $erro): ?>
        <div class="alerta alerta-erro" role="alert"><?= e($erro) ?></div>
    <?php endforeach; ?>

    <form method="post" action="cadastro.php" data-validar novalidate>
        <?= csrf_campo() ?>
        <div class="campo">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?= e($nome) ?>" maxlength="100" autocomplete="name" required>
        </div>
        <div class="campo">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
        </div>
        <div class="campo">
            <label for="senha">Senha</label>
            <div class="campo-senha">
                <input type="password" id="senha" name="senha" minlength="6" autocomplete="new-password" required>
                <button type="button" class="btn-olho" data-alvo="senha" aria-label="Mostrar senha">Mostrar</button>
            </div>
            <small class="dica">Mínimo de 6 caracteres.</small>
        </div>
        <div class="campo">
            <label for="confirmacao">Confirmar senha</label>
            <div class="campo-senha">
                <input type="password" id="confirmacao" name="confirmacao" data-igual-a="senha" autocomplete="new-password" required>
                <button type="button" class="btn-olho" data-alvo="confirmacao" aria-label="Mostrar confirmação da senha">Mostrar</button>
            </div>
        </div>
        <button class="botao" type="submit">Criar conta</button>
    </form>

    <p class="troca">Já tem conta? <a href="login.php">Entrar</a></p>
</section>
<?php require 'includes/rodape.php'; ?>
