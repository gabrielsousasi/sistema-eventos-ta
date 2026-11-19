<?php
require_once 'includes/funcoes.php';
exigir_login();

$valores = ['nome' => '', 'descricao' => '', 'data' => '', 'horario' => '', 'local' => ''];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valores as $campo => $vazio) {
        $valores[$campo] = trim($_POST[$campo] ?? '');
    }

    if (!csrf_valido()) {
        $erros[] = 'A página expirou. Tente novamente.';
    }
    if ($valores['nome'] === '' || tamanho_texto($valores['nome']) > 120) {
        $erros[] = 'Informe o nome do evento (até 120 caracteres).';
    }
    if ($valores['descricao'] === '' || tamanho_texto($valores['descricao']) > 1000) {
        $erros[] = 'Informe a descrição (até 1000 caracteres).';
    }
    if (!data_valida($valores['data'])) {
        $erros[] = 'Informe uma data válida.';
    } elseif ($valores['data'] < date('Y-m-d')) {
        $erros[] = 'A data do evento não pode estar no passado.';
    }
    if (!horario_valido($valores['horario'])) {
        $erros[] = 'Informe um horário válido.';
    }
    if ($valores['local'] === '' || tamanho_texto($valores['local']) > 120) {
        $erros[] = 'Informe o local (até 120 caracteres).';
    }

    if (!$erros) {
        $eventos = ler_json('eventos.json');
        $novo = $valores;
        $novo['id'] = proximo_id($eventos);
        $novo['criado_por'] = $_SESSION['usuario_id'];
        $eventos[] = $novo;

        if (salvar_json('eventos.json', $eventos)) {
            mensagem('sucesso', 'Evento cadastrado com sucesso.');
            header('Location: detalhes.php?id=' . $novo['id']);
            exit;
        }
        $erros[] = 'Não foi possível salvar o evento. Verifique as permissões da pasta dados/.';
    }
}

$titulo = 'Novo evento';
require 'includes/cabecalho.php';
?>
<section class="cartao-form cartao-largo">
    <h1>Novo evento</h1>

    <?php foreach ($erros as $erro): ?>
        <div class="alerta alerta-erro" role="alert"><?= e($erro) ?></div>
    <?php endforeach; ?>

    <form method="post" action="criar-evento.php" data-validar novalidate>
        <?= csrf_campo() ?>
        <div class="campo">
            <label for="nome">Nome do evento</label>
            <input type="text" id="nome" name="nome" value="<?= e($valores['nome']) ?>" maxlength="120" required>
        </div>
        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="5" maxlength="1000" required><?= e($valores['descricao']) ?></textarea>
        </div>
        <div class="linha-campos">
            <div class="campo">
                <label for="data">Data</label>
                <input type="date" id="data" name="data" value="<?= e($valores['data']) ?>" data-futuro required>
            </div>
            <div class="campo">
                <label for="horario">Horário</label>
                <input type="time" id="horario" name="horario" value="<?= e($valores['horario']) ?>" required>
            </div>
        </div>
        <div class="campo">
            <label for="local">Local</label>
            <input type="text" id="local" name="local" value="<?= e($valores['local']) ?>" maxlength="120" required>
        </div>
        <button class="botao" type="submit">Cadastrar evento</button>
    </form>
</section>
<?php require 'includes/rodape.php'; ?>
