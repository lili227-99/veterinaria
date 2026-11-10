<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
auth_require_page();
require_once __DIR__ . '/database.php';

$paginasPermitidas = ['inicio', 'clientes', 'mascotas', 'turnos'];
$pages = $_GET['pages'] ?? 'inicio';

if (!in_array($pages, $paginasPermitidas, true)) {
    http_response_code(404);
    $pages = 'inicio';
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/pages/' . $pages . '.php';
require __DIR__ . '/includes/footer.php';
