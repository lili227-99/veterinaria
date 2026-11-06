
<?php

header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido. Utilizá POST."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_turno = filter_var(
    $_POST["id_turno"] ?? null,
    FILTER_VALIDATE_INT
);

if (!$id_turno || $id_turno <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "El ID del turno no es válido."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $consulta = $conexion->prepare(
        "SELECT id_turno, estado
         FROM turnos
         WHERE id_turno = :id_turno"
    );

    $consulta->execute([
        ":id_turno" => $id_turno
    ]);

    $turno = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "mensaje" => "El turno no existe."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($turno["estado"] === "cancelado") {
        echo json_encode([
            "success" => true,
            "mensaje" => "El turno ya estaba cancelado."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conexion->prepare(
        "UPDATE turnos
         SET estado = 'cancelado'
         WHERE id_turno = :id_turno"
    );

    $stmt->execute([
        ":id_turno" => $id_turno
    ]);

    echo json_encode([
        "success" => true,
        "mensaje" => "Turno cancelado correctamente.",
        "id_turno" => (int) $id_turno
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Error al cancelar turno: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo cancelar el turno."
    ], JSON_UNESCAPED_UNICODE);
}
