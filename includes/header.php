<?php
require_once __DIR__ . '/auth.php';
auth_start_session();

$pages = $_GET['pages'] ?? 'inicio';
$menu = [
    'inicio'   => ['🏠', 'Inicio'],
    'clientes' => ['👤', 'Clientes'],
    'mascotas' => ['🐾', 'Mascotas'],
    'turnos'   => ['📅', 'Turnos'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <?php
    $css_pagina = __DIR__ . '/../assets/css/' . $pages . '.css';
    if (file_exists($css_pagina)): ?>
        <link rel="stylesheet" href="assets/css/<?= htmlspecialchars($pages, ENT_QUOTES, 'UTF-8') ?>.css">
    <?php endif; ?>
</head>
<body>
<button id="btnMenu" class="btn-menu" aria-label="Abrir menú">☰</button>
<div class="layout">
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-logo">
            <span>🐶</span>
            <h2>Veterinaria</h2>
        </div>
        <nav class="sidebar-nav">
            <?php foreach ($menu as $clave => [$icono, $texto]): ?>
                <a href="index.php?pages=<?= urlencode($clave) ?>" class="<?= $pages === $clave ? 'activo' : '' ?>">
                    <span class="icono"><?= $icono ?></span>
                    <span class="texto"><?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-footer">
            <small>Sistema de gestión</small>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(auth_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn-cerrar-sesion">Cerrar sesión</button>
            </form>
        </div>
    </aside>
    <main class="contenido">
