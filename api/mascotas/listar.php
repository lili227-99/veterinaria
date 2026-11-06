<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../database.php";

// Compatibilidad con ambos nombres de conexión
if (!isset($conexion) && isset($pdo)) {
    $conexion = $pdo;
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido. Usá GET."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    if (!isset($conexion) || !$conexion instanceof PDO) {
        throw new Exception("No existe una conexión PDO válida.");
    }

    $sql = "
        SELECT
            m.id_mascota,
            m.id_cliente,
            m.nombre,
            m.especie,
            m.raza,
            m.sexo,
            m.fecha_nacimiento,
            m.peso,
            m.color,
            m.observaciones,
            c.nombre AS nombre_cliente,
            c.apellido AS apellido_cliente
        FROM mascotas m
        LEFT JOIN clientes c ON c.id_cliente = m.id_cliente
        WHERE m.activo = 1
        ORDER BY m.id_mascota DESC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "mascotas" => ($mascotas),
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error al listar mascotas: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error al obtener la lista de mascotas."
    ], JSON_UNESCAPED_UNICODE);
}