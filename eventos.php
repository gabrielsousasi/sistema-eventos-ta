<?php
require_once 'includes/funcoes.php';
exigir_login();

$eventos = ordenar_eventos(ler_json('eventos.json'));

$titulo = 'Eventos';
require 'includes/cabecalho.php';
?>
<div class="secao-titulo">
    <h1>Eventos</h1>
    <a class="botao" href="criar-evento.php">Novo evento</a>
</div>

<?php if (!$eventos): ?>
    <div class="vazio">
        <p>Nenhum evento cadastrado ainda.</p>
        <a class="botao" href="criar-evento.php">Cadastrar o primeiro evento</a>
    </div>
<?php else: ?>
    <div class="lista">
        <?php foreach ($eventos as $evento) { mostrar_evento($evento); } ?>
    </div>
<?php endif; ?>
<?php require 'includes/rodape.php'; ?>
