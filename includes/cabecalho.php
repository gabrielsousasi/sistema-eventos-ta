<?php
require_once __DIR__ . '/funcoes.php';
$msg = pegar_mensagem();
$logado = usuario_logado();
$atual = basename($_SERVER['SCRIPT_NAME']);

function link_menu($arquivo, $texto, $atual)
{
    $classe = ($arquivo === $atual) ? ' class="ativo" aria-current="page"' : '';
    echo '<a href="' . $arquivo . '"' . $classe . '>' . $texto . '</a>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Início') ?> | To Atoa - Events</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<header class="topo">
    <div class="topo-interno">
        <a class="marca" href="<?= $logado ? 'inicio.php' : 'login.php' ?>" aria-label="To Atoa - Events, página inicial">
            <img src="img/logo-nome.png" alt="To Atoa" class="marca-logo">
            <span class="marca-texto">Events</span>
        </a>
        <?php if ($logado): ?>
            <button class="menu-botao" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
            <nav class="menu" id="menu" aria-label="Navegação principal">
                <?php
                link_menu('inicio.php', 'Início', $atual);
                link_menu('eventos.php', 'Eventos', $atual);
                link_menu('criar-evento.php', 'Novo evento', $atual);
                link_menu('meus-agendamentos.php', 'Meus agendamentos', $atual);
                ?>
                <a class="sair" href="logout.php">Sair</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="conteudo">
<?php if ($msg): ?>
    <div class="alerta alerta-<?= e($msg['tipo']) ?>" role="alert"><?= e($msg['texto']) ?></div>
<?php endif; ?>
