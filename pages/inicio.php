<?php
$resumen = [
    "clientes"  => 0,
    "mascotas"  => 0,
    "hoy"       => 0,
    "pendientes" => 0
];
$error_resumen = false;
try {
    $resumen["clientes"] = (int) $conexion
        ->query("SELECT COUNT(*) FROM clientes WHERE activo = 1")
        ->fetchColumn();

    $resumen["mascotas"] = (int) $conexion
        ->query("SELECT COUNT(*) FROM mascotas WHERE activo = 1")
        ->fetchColumn();

    $resumen["hoy"] = (int) $conexion
        ->query(
            "SELECT COUNT(*) FROM turnos
             WHERE DATE(fecha_hora) = CURDATE()
               AND estado <> 'cancelado'"
        )
        ->fetchColumn();

    $resumen["pendientes"] = (int) $conexion
        ->query("SELECT COUNT(*) FROM turnos WHERE estado = 'pendiente'")
        ->fetchColumn();

} catch (PDOException $e) {
    error_log("Error en el inicio: " . $e->getMessage());
    $error_resumen = true;
}
?>

<div class="contenedor">
    <h1>Bienvenido al sistema de gestión de la veterinaria</h1>

    <?php if ($error_resumen): ?>
        <p class="error">No se pudo cargar el resumen. Por favor, inténtalo más tarde.</p>
    <?php else: ?>
        <div class="resumen">
            <div class="resumen-item">
                <span class="resumen-icono">👤</span>
                <span class="resumen-texto">Clientes activos: <?= $resumen["clientes"] ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-icono">🐾</span>
                <span class="resumen-texto">Mascotas activas: <?= $resumen["mascotas"] ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-icono">📅</span>
                <span class="resumen-texto">Turnos hoy: <?= $resumen["hoy"] ?></span>
            </div>
            <div class="resumen-item">
                <span class="resumen-icono">⏳</span>
                <span class="resumen-texto">Turnos pendientes: <?= $resumen["pendientes"] ?></span>
            </div>
        </div>
    <?php endif; ?>

    
  