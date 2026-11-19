<?php
require_once 'includes/funcoes.php';
exigir_login();

$meus = [];
foreach (ler_json('agendamentos.json') as $ag) {
    if ($ag['usuario_id'] == $_SESSION['usuario_id']) {
        $evento = buscar_evento($ag['evento_id']);
        if ($evento) {
            $meus[] = $evento;
        }
    }
}
$meus = ordenar_eventos($meus);

$titulo = 'Meus agendamentos';
require 'includes/cabecalho.php';
?>
<h1>Meus agendamentos</h1>

<?php if (!$meus): ?>
    <div class="vazio">
        <p>Você ainda não agendou nenhum evento.</p>
        <a class="botao" href="eventos.php">Ver eventos</a>
    </div>
<?php else: ?>
    <div class="lista">
        <?php foreach ($meus as $evento):
            $acao = '<form method="post" action="cancelar.php" class="form-cancelar">'
                . csrf_campo()
                . '<input type="hidden" name="evento_id" value="' . (int) $evento['id'] . '">'
                . '<button class="botao botao-perigo" type="submit">Cancelar</button>'
                . '</form>';
            mostrar_evento($evento, $acao);
        endforeach; ?>
    </div>
<?php endif; ?>
<?php require 'includes/rodape.php'; ?>
