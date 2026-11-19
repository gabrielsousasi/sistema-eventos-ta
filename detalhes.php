<?php
require_once 'includes/funcoes.php';
exigir_login();

$evento = buscar_evento((int) ($_GET['id'] ?? 0));
if (!$evento) {
    mensagem('erro', 'Evento não encontrado.');
    header('Location: eventos.php');
    exit;
}

$agendado = buscar_agendamento($_SESSION['usuario_id'], $evento['id']) !== null;
$passado = evento_passado($evento);
list($dia, $mes) = partes_data($evento['data']);

$titulo = $evento['nome'];
require 'includes/cabecalho.php';
?>
<p><a href="eventos.php">&larr; Voltar para eventos</a></p>

<article class="detalhe">
    <div class="calendario calendario-grande" aria-hidden="true">
        <span class="dia"><?= $dia ?></span>
        <span class="mes"><?= e($mes) ?></span>
    </div>

    <h1><?= e($evento['nome']) ?></h1>

    <dl class="dados">
        <div><dt>Data</dt><dd><?= e(data_br($evento['data'])) ?></dd></div>
        <div><dt>Horário</dt><dd><?= e($evento['horario']) ?></dd></div>
        <div><dt>Local</dt><dd><?= e($evento['local']) ?></dd></div>
    </dl>

    <h2>Sobre o evento</h2>
    <p class="descricao"><?= nl2br(e($evento['descricao'])) ?></p>

    <div class="detalhe-acao">
        <?php if ($agendado): ?>
            <p class="selo selo-ok">Você está agendado neste evento</p>
            <form method="post" action="cancelar.php" class="form-cancelar">
                <?= csrf_campo() ?>
                <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                <button class="botao botao-perigo" type="submit">Cancelar agendamento</button>
            </form>
        <?php elseif ($passado): ?>
            <p class="selo selo-encerrado">Evento encerrado. Não é possível agendar.</p>
        <?php else: ?>
            <form method="post" action="agendar.php">
                <?= csrf_campo() ?>
                <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                <button class="botao" type="submit">Agendar presença</button>
            </form>
        <?php endif; ?>
    </div>
</article>
<?php require 'includes/rodape.php'; ?>
