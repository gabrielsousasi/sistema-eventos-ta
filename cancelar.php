<?php
require_once 'includes/funcoes.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: meus-agendamentos.php');
    exit;
}

$evento_id = (int) ($_POST['evento_id'] ?? 0);

if (!csrf_valido()) {
    mensagem('erro', 'A página expirou. Tente novamente.');
} elseif (!buscar_agendamento($_SESSION['usuario_id'], $evento_id)) {
    mensagem('erro', 'Você não tem agendamento neste evento.');
} else {
    $restantes = [];
    foreach (ler_json('agendamentos.json') as $ag) {
        if (!($ag['usuario_id'] == $_SESSION['usuario_id'] && $ag['evento_id'] == $evento_id)) {
            $restantes[] = $ag;
        }
    }
    if (salvar_json('agendamentos.json', $restantes)) {
        mensagem('sucesso', 'Agendamento cancelado.');
    } else {
        mensagem('erro', 'Não foi possível cancelar. Tente novamente.');
    }
}

header('Location: meus-agendamentos.php');
exit;
