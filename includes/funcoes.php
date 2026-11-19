<?php
/**
 * Funções compartilhadas por todas as páginas.
 * Inclua com: require_once 'includes/funcoes.php';
 */

date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DIR_DADOS', __DIR__ . '/../dados/');

/* ---------- Armazenamento em JSON ---------- */

function ler_json($arquivo)
{
    $caminho = DIR_DADOS . $arquivo;
    if (!file_exists($caminho)) {
        return [];
    }
    $dados = json_decode(file_get_contents($caminho), true);
    return is_array($dados) ? $dados : [];
}

function salvar_json($arquivo, $dados)
{
    $json = json_encode(array_values($dados), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(DIR_DADOS . $arquivo, $json, LOCK_EX) !== false;
}

function proximo_id($lista)
{
    $maior = 0;
    foreach ($lista as $item) {
        if ($item['id'] > $maior) {
            $maior = $item['id'];
        }
    }
    return $maior + 1;
}

/* ---------- Segurança e sessão ---------- */

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** Conta caracteres (funciona mesmo sem a extensão mbstring). */
function tamanho_texto($texto)
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : preg_match_all('/./us', $texto);
}

function usuario_logado()
{
    return !empty($_SESSION['usuario_id']);
}

function exigir_login()
{
    if (!usuario_logado()) {
        mensagem('erro', 'Entre na sua conta para acessar essa página.');
        header('Location: login.php');
        exit;
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido()
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/* ---------- Mensagens de sucesso e erro ---------- */

function mensagem($tipo, $texto)
{
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}

function pegar_mensagem()
{
    if (empty($_SESSION['mensagem'])) {
        return null;
    }
    $msg = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
    return $msg;
}

/* ---------- Eventos e agendamentos ---------- */

function buscar_evento($id)
{
    foreach (ler_json('eventos.json') as $evento) {
        if ($evento['id'] == $id) {
            return $evento;
        }
    }
    return null;
}

function ordenar_eventos($eventos)
{
    usort($eventos, function ($a, $b) {
        return strcmp($a['data'] . ' ' . $a['horario'], $b['data'] . ' ' . $b['horario']);
    });
    return $eventos;
}

function evento_passado($evento)
{
    return strtotime($evento['data'] . ' ' . $evento['horario']) < time();
}

function buscar_agendamento($usuario_id, $evento_id)
{
    foreach (ler_json('agendamentos.json') as $ag) {
        if ($ag['usuario_id'] == $usuario_id && $ag['evento_id'] == $evento_id) {
            return $ag;
        }
    }
    return null;
}

function data_valida($data)
{
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $p)) {
        return false;
    }
    return checkdate((int) $p[2], (int) $p[3], (int) $p[1]);
}

function horario_valido($horario)
{
    return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $horario);
}

/* ---------- Formatação ---------- */

function data_br($data)
{
    $p = explode('-', $data);
    return count($p) === 3 ? $p[2] . '/' . $p[1] . '/' . $p[0] : $data;
}

function partes_data($data)
{
    $meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
    $p = explode('-', $data);
    return [(int) $p[2], $meses[(int) $p[1] - 1]];
}

/**
 * Imprime um evento em formato de cartão.
 * $acoes recebe HTML extra (botões) já escapado pelo chamador.
 */
function mostrar_evento($evento, $acoes = '')
{
    list($dia, $mes) = partes_data($evento['data']);
    ?>
    <article class="evento">
        <div class="calendario" aria-hidden="true">
            <span class="dia"><?= $dia ?></span>
            <span class="mes"><?= e($mes) ?></span>
        </div>
        <div class="evento-info">
            <h3><a href="detalhes.php?id=<?= (int) $evento['id'] ?>"><?= e($evento['nome']) ?></a></h3>
            <p class="meta"><?= e(data_br($evento['data'])) ?> às <?= e($evento['horario']) ?> &middot; <?= e($evento['local']) ?></p>
            <?php if (evento_passado($evento)): ?>
                <p class="selo selo-encerrado">Evento encerrado</p>
            <?php endif; ?>
        </div>
        <div class="evento-acoes">
            <a class="botao botao-secundario" href="detalhes.php?id=<?= (int) $evento['id'] ?>">Ver detalhes</a>
            <?= $acoes ?>
        </div>
    </article>
    <?php
}
