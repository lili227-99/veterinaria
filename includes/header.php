
<?php
$paginasPermitidas = ["inicio", "clientes", "mascotas", "turnos"];
$pages = $_GET["pages"] ?? "inicio";
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
        <link rel="stylesheet" href="assets/css/<?= htmlspecialchars($pages) ?>.css">
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
                <a href="index.php?pages=<?= $clave ?>"
                   class="<?= $pages === $clave ? 'activo' : '' ?>">
                    <span class="icono"><?= $icono ?></span>
                    <span class="texto"><?= $texto ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-footer">
            <small>Sistema de gestión</small>
        </div>
    </aside>

    <main class="contenido">
          