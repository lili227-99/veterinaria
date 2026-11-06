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

$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$dni = trim($_POST["dni"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$email = trim($_POST["email"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");
if ($nombre === "" || $apellido === "") {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "El nombre y el apellido son obligatorios."
    ]);
    exit;
}
//validar campos obligatorios
if (mb_strlen($nombre) > 80 ||
    mb_strlen($apellido) > 80 ||
    mb_strlen($dni) > 20 ||
    mb_strlen($telefono) > 30 ||
    mb_strlen($email) > 120 ||
    mb_strlen($direccion) > 200
) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "Uno o más campos superan la longitud permitida."
    ]);
    exit;
}
//convertir campos opcionales vacíos a NULL
$dni = $dni === "" ? null : $dni;
$telefono = $telefono === "" ? null : $telefono;
$email = $email === "" ? null : $email;
$direccion = $direccion === "" ? null : $direccion;

 try {
    $sql = "INSERT INTO clientes
            (nombre, apellido, dni, telefono, email, direccion)
            VALUES
            (:nombre, :apellido, :dni, :telefono, :email, :direccion)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":dni" => $dni,
        ":telefono" => $telefono,
        ":email" => $email,
        ":direccion" => $direccion
    ]);
    http_response_code(201);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => true,
        "mensaje" => "Cliente registrado correctamente.",
        "id_cliente" => (int) $conexion->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log("Error al crear cliente: " . $e->getMessage());
    if ($e->getCode() === "23000") {
        http_response_code(409);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "success" => false,
            "mensaje" =>  "Ya existe un cliente con ese DNI o correo electrónico."
        ]);
        exit;
    }

    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "Error al registrar el cliente. Por favor, inténtalo más tarde."
    ]);

}