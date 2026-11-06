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
        "mensaje" => "Método no permitido. Utilizá POST."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Datos del formulario
$id_mascota = filter_input(INPUT_POST, "id_mascota", FILTER_VALIDATE_INT);
$id_cliente = filter_input(INPUT_POST, "id_cliente", FILTER_VALIDATE_INT);

$nombre = trim($_POST["nombre"] ?? "");
$especie = trim($_POST["especie"] ?? "");
$raza = trim($_POST["raza"] ?? "");
$sexo = strtolower(trim((string) ($_POST["sexo"] ?? "no_especificado")));
$fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "");
$peso_texto = trim($_POST["peso"] ?? "");
$color = trim($_POST["color"] ?? "");
$observaciones = trim($_POST["observaciones"] ?? "");

// Validar IDs
if (!$id_mascota || $id_mascota <= 0 || !$id_cliente || $id_cliente <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => "El ID de la mascota o del cliente no es válido."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// Validar campos obligatorios
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

    // 1. Verificar que la mascota exista y esté activa
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
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "mensaje" => "La mascota no existe o está desactivada."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // 2. Verificar que el cliente exista y esté activo
    $consulta = $conexion->prepare(
        "SELECT id_cliente
         FROM clientes
         WHERE id_cliente = :id_cliente
           AND activo = 1"
    );

    $consulta->execute([
        ":id_cliente" => $id_cliente
    ]);

    if (!$consulta->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "mensaje" => "El cliente seleccionado no existe o está desactivado."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // 3. Actualizar la mascota
    $sql = "
        UPDATE mascotas
        SET
            id_cliente = :id_cliente,
            nombre = :nombre,
            especie = :especie,
            raza = :raza,
            sexo = :sexo,
            fecha_nacimiento = :fecha_nacimiento,
            peso = :peso,
            color = :color,
            observaciones = :observaciones
        WHERE id_mascota = :id_mascota
          AND activo = 1
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
        ":observaciones" => $observaciones !== "" ? $observaciones : null,
        ":id_mascota" => $id_mascota
    ]);

    if (!$resultado) {
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "mensaje" => "No se pudo actualizar la mascota."
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    echo json_encode([
        "success" => true,
        "mensaje" => "Mascota actualizada correctamente.",
        "id_mascota" => (int) $id_mascota
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("Error al editar mascota: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "mensaje" => "Ocurrió un error en la base de datos al actualizar la mascota."
    ], JSON_UNESCAPED_UNICODE);
}