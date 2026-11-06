<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../database.php";

if (!isset($conexion) && isset($pdo)) {
    $conexion = $pdo;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido. Usá POST."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$id_mascota = filter_input(INPUT_POST, "id_mascota", FILTER_VALIDATE_INT);

if (!$id_mascota || $id_mascota <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "El ID de la mascota no es válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    if (!isset($conexion) || !$conexion instanceof PDO) {
        throw new Exception("No existe una conexión PDO válida.");
    }

    $check = $conexion->prepare(
        "SELECT id_mascota
         FROM mascotas
         WHERE id_mascota = :id_mascota"
    );

    $check->execute([
        ":id_mascota" => $id_mascota
    ]);

    if (!$check->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "mensaje" => "La mascota no existe."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $sql = "UPDATE mascotas SET activo = 0 WHERE id_mascota = :id_mascota";
    $stmt = $conexion->prepare($sql);

    $resultado = $stmt->execute([
        ":id_mascota" => $id_mascota
    ]);

    if (!$resultado) {
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "mensaje" => "No se pudo eliminar la mascota."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    echo json_encode([
        "success" => true,
        "mensaje" => "Mascota eliminada correctamente.",
        "id_mascota" => $id_mascota
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error al eliminar mascota: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error al eliminar la mascota."
    ], JSON_UNESCAPED_UNICODE);
}