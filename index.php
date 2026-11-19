<?php
require_once 'includes/funcoes.php';

header('Location: ' . (usuario_logado() ? 'inicio.php' : 'login.php'));
exit;
