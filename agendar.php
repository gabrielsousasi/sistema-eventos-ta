<?php
require_once 'includes/funcoes.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: eventos.php');
    exit;
}

$evento_id = (int) ($_POST['evento_id'] ?? 0);
$destino = 'detalhes.php?id=' . $evento_id;
$evento = buscar_evento($evento_id);

if (!csrf_valido()) {
    mensagem('erro', 'A página expirou. Tente novamente.');
} elseif (!$evento) {
    mensagem('erro', 'Evento não encontrado.');
    $destino = 'eventos.php';
} elseif (evento_passado($evento)) {
    mensagem('erro', 'Esse evento já foi encerrado.');
} elseif (buscar_agendamento($_SESSION['usuario_id'], $evento_id)) {
    mensagem('erro', 'Você já está agendado neste evento.');
} else {
    $agendamentos = ler_json('agendamentos.json');
    $agendamentos[] = [
        'id' => proximo_id($agendamentos),
        'usuario_id' => $_SESSION['usuario_id'],
        'evento_id' => $evento_id,
        'criado_em' => date('Y-m-d H:i:s'),
    ];
    if (salvar_json('agendamentos.json', $agendamentos)) {
        mensagem('sucesso', 'Agendamento realizado com sucesso.');
    } else {
        mensagem('erro', 'Não foi possível salvar o agendamento. Tente novamente.');
    }
}

header('Location: ' . $destino);
exit;
