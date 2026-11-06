<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../database.php";

// Compatibilidad con ambos nombres de conexión
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

// Recibir datos
$id_cliente = filter_input(INPUT_POST, "id_cliente", FILTER_VALIDATE_INT);

$nombre = trim($_POST["nombre"] ?? "");
$especie = trim($_POST["especie"] ?? "");
$raza = trim($_POST["raza"] ?? "");
$sexo = strtolower(trim((string) ($_POST["sexo"] ?? "no_especificado")));
$fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "");
$peso_texto = trim($_POST["peso"] ?? "");
$color = trim($_POST["color"] ?? "");
$observaciones = trim($_POST["observaciones"] ?? "");

// Validar cliente
if (!$id_cliente || $id_cliente <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "El ID del cliente no es válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Validar obligatorios
if ($nombre === "" || $especie === "") {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "El nombre y la especie son obligatorios."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Validar longitudes
if (
    mb_strlen($nombre) > 80 ||
    mb_strlen($especie) > 50 ||
    mb_strlen($raza) > 80 ||
    mb_strlen($color) > 60
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "Uno de los campos supera la longitud permitida."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Validar sexo
if (!in_array($sexo, ["macho", "hembra", "no_especificado"], true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "El sexo seleccionado no es válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Validar fecha de nacimiento
$fecha = null;

if ($fecha_nacimiento !== "") {
    $fecha_objeto = DateTimeImmutable::createFromFormat("!Y-m-d", $fecha_nacimiento);

    $fecha_valida = $fecha_objeto !== false
        && $fecha_objeto->format("Y-m-d") === $fecha_nacimiento
        && $fecha_objeto <= new DateTimeImmutable("today");

    if (!$fecha_valida) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "mensaje" => "La fecha de nacimiento no es válida."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $fecha = $fecha_nacimiento;
}

// Validar peso
$peso = null;

if ($peso_texto !== "") {
    $peso_normalizado = str_replace(",", ".", $peso_texto);

    if (
        !is_numeric($peso_normalizado) ||
        (float) $peso_normalizado <= 0 ||
        (float) $peso_normalizado > 9999.99
    ) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "mensaje" => "El peso debe ser mayor que cero y no superar 9999.99 kg."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $peso = (float) $peso_normalizado;
}

try {
    if (!isset($conexion) || !$conexion instanceof PDO) {
        throw new Exception("No existe una conexión PDO válida.");
    }

    // Validar que el cliente exista y esté activo
    $clienteCheck = $conexion->prepare(
        "SELECT id_cliente
         FROM clientes
         WHERE id_cliente = :id_cliente
           AND activo = 1"
    );

    $clienteCheck->execute([
        ":id_cliente" => $id_cliente
    ]);

    if (!$clienteCheck->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "mensaje" => "El cliente seleccionado no existe o está desactivado."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $sql = "
        INSERT INTO mascotas (
            id_cliente,
            nombre,
            especie,
            raza,
            sexo,
            fecha_nacimiento,
            peso,
            color,
            observaciones,
            activo
        )
        VALUES (
            :id_cliente,
            :nombre,
            :especie,
            :raza,
            :sexo,
            :fecha_nacimiento,
            :peso,
            :color,
            :observaciones,
            1
        )
    ";

    $stmt = $conexion->prepare($sql);

    $resultado = $stmt->execute([
        ":id_cliente" => $id_cliente,
        ":nombre" => $nombre,
        ":especie" => $especie,
        ":raza" => $raza !== "" ? $raza : null,
        ":sexo" => $sexo,
        ":fecha_nacimiento" => $fecha,
        ":peso" => $peso,
        ":color" => $color !== "" ? $color : null,
        ":observaciones" => $observaciones !== "" ? $observaciones : null
    ]);

    if (!$resultado) {
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "mensaje" => "No se pudo crear la mascota."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $id_mascota = (int) $conexion->lastInsertId();

    echo json_encode([
        "success" => true,
        "mensaje" => "Mascota creada correctamente.",
        "id_mascota" => $id_mascota
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error al crear mascota: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error al crear la mascota."
    ], JSON_UNESCAPED_UNICODE);
}