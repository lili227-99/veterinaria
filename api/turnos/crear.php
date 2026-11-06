<?php
// api/turnos/crear.php
// Registra un turno nuevo.

header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once __DIR__ . "/../../database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "mensaje" => "Método no permitido. Utilizá POST."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_mascota = filter_var(
    $_POST["id_mascota"] ?? null,
    FILTER_VALIDATE_INT
);

$fecha_hora_texto = trim($_POST["fecha_hora"] ?? "");
$motivo = trim($_POST["motivo"] ?? "");
$estado = trim($_POST["estado"] ?? "pendiente");

$estados_validos = [
    "pendiente",
    "confirmado",
    "atendido",
    "cancelado",
    "ausente"
];

if (!$id_mascota || $id_mascota <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "Seleccioná una mascota válida."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($fecha_hora_texto === "" || $motivo === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "La fecha, la hora y el motivo son obligatorios."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (mb_strlen($motivo) > 255) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "El motivo no puede superar los 255 caracteres."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($estado, $estados_validos, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "El estado seleccionado no es válido."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Aceptar la fecha del formulario datetime-local (2026-10-20T10:30).
$fechaObjeto = DateTime::createFromFormat(
    "!Y-m-d\TH:i",
    $fecha_hora_texto
);

if (
    !$fechaObjeto ||
    $fechaObjeto->format("Y-m-d\TH:i") !== $fecha_hora_texto
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "La fecha y hora no tienen un formato válido."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// No permitir turnos en el pasado (se tolera el minuto actual).
$ahora = new DateTime("now");
$ahora->setTime((int) $ahora->format("H"), (int) $ahora->format("i"), 0);

if ($fechaObjeto < $ahora) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "mensaje" => "No se puede registrar un turno en una fecha u hora pasada."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$fecha_hora = $fechaObjeto->format("Y-m-d H:i:s");

try {
    // Comprobar que la mascota exista y esté activa.
    $consulta = $conexion->prepare(
        "SELECT id_mascota
         FROM mascotas
         WHERE id_mascota = :id_mascota
           AND activo = 1"
    );

    $consulta->execute([
        ":id_mascota" => $id_mascota
    ]);

    if (!$consulta->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "mensaje" => "La mascota no existe o está desactivada."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $sql = "INSERT INTO turnos (
                id_mascota,
                fecha_hora,
                motivo,
                estado
            ) VALUES (
                :id_mascota,
                :fecha_hora,
                :motivo,
                :estado
            )";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        ":id_mascota"    => $id_mascota,
        ":fecha_hora"    => $fecha_hora,
        ":motivo"        => $motivo,
        ":estado"        => $estado,
    ]);

    http_response_code(201);

    echo json_encode([
        "success"  => true,
        "mensaje"  => "Turno registrado correctamente.",
        "id_turno" => (int) $conexion->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Error al crear turno: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo registrar el turno."
    ], JSON_UNESCAPED_UNICODE);
}