<?php
require_once __DIR__ . "/../../database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido. Utilizá POST."
    ]);
    exit;
}

$id_cliente = filter_var($_POST["id_cliente"] ?? null, FILTER_VALIDATE_INT);

if ($id_cliente === false || $id_cliente === null || $id_cliente <= 0) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "El ID del cliente no es válido."
    ]);
    exit;
}
try {
    $consulta = $conexion->prepare(
        "SELECT id_cliente
         FROM clientes
         WHERE id_cliente = :id AND activo = 1"
    );

    $consulta->execute([
        ":id" => $id_cliente
    ]);

    if (!$consulta->fetch()) {
        http_response_code(404);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "success" => false,
            "mensaje" => "El cliente no existe o ya está desactivado."
        ]);
        exit;
    }

    $sql = "UPDATE clientes
            SET activo = 0
            WHERE id_cliente = :id";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ":id" => $id_cliente
    ]);

    http_response_code(200);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => true,
        "mensaje" => "Cliente desactivado correctamente.",
        "id_cliente" => (int) $id_cliente
    ]);
} catch (PDOException $e) {
    error_log("Error al desactivar cliente: " . $e->getMessage());
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo desactivar el cliente."
    ]);
}





?>

   