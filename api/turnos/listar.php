<?php
// api/turnos/listar.php
// Devuelve los turnos (con mascota y cliente) de las mascotas activas.

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../database.php";

try {
    $sql = "SELECT
                t.id_turno,
                t.id_mascota,
                t.fecha_hora,
                t.motivo,
                t.estado,

                m.nombre AS nombre_mascota,
                m.especie,
                m.raza,

                c.id_cliente,
                c.nombre AS nombre_cliente,
                c.apellido AS apellido_cliente

            FROM turnos t

            INNER JOIN mascotas m
                ON t.id_mascota = m.id_mascota

            INNER JOIN clientes c
                ON m.id_cliente = c.id_cliente

            WHERE m.activo = 1

            ORDER BY t.fecha_hora DESC, t.id_turno DESC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "turnos"  => $turnos
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Error al listar turnos: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudieron cargar los turnos."
    ], JSON_UNESCAPED_UNICODE);
}