<?php
require_once 'includes/funcoes.php';
exigir_login();

$nome_completo = $_SESSION['usuario_nome'];
$primeiro_nome = explode(' ', trim($nome_completo))[0];

$proximos = [];
foreach (ordenar_eventos(ler_json('eventos.json')) as $evento) {
    if (!evento_passado($evento)) {
        $proximos[] = $evento;
    }
}
$proximos = array_slice($proximos, 0, 3);

$total_agendados = 0;
foreach (ler_json('agendamentos.json') as $ag) {
    if ($ag['usuario_id'] == $_SESSION['usuario_id']) {
        $total_agendados++;
    }
}

$titulo = 'Início';
require 'includes/cabecalho.php';
?>
<section class="hero">
<div>
<h1>Olá, <?= e($primeiro_nome) ?></h1>
<p class="apoio">
    <?php if ($total_agendados === 0): ?>
        Você ainda não agendou nenhum evento.
    <?php else: ?>
        Você tem <?= $total_agendados ?> <?= $total_agendados === 1 ? 'agendamento' : 'agendamentos' ?>.
    <?php endif; ?>
</p>
<a class="botao" href="criar-evento.php">Criar evento</a>
</div>
<img src="img/logo-republica.png" alt="" class="hero-logo" aria-hidden="true">
</section>

<section>
    <div class="secao-titulo">
        <h2>Próximos eventos</h2>
        <a href="eventos.php">Ver todos</a>
    </div>

    <?php if (!$proximos): ?>
        <div class="vazio">
            <p>Nenhum evento futuro cadastrado.</p>
            <a class="botao" href="criar-evento.php">Cadastrar evento</a>
        </div>
    <?php else: ?>
        <div class="lista">
            <?php foreach ($proximos as $evento) { mostrar_evento($evento); } ?>
        </div>
    <?php endif; ?>
</section>
<?php require 'includes/rodape.php'; ?>
