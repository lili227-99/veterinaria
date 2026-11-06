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
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");
$dni = trim($_POST["dni"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$email = trim($_POST["email"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

//validar id_cliente
if ($id_cliente === false || $id_cliente === null || $id_cliente <= 0) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "El ID del cliente no es válido."
    ]);
    exit;
}

//validar campos obligatorios
if ($nombre === "" || $apellido === "") {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "El nombre y el apellido son obligatorios."
    ]);
    exit;
}

//validar longitudes máximas
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
//validar email si fue ingresado
if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "El correo electrónico no es válido."
    ]);
    exit;
}

//convertir campos opcionales vacíos a NULL
$dni = $dni === "" ? null : $dni;
$telefono = $telefono === "" ? null : $telefono;
$email = $email === "" ? null : $email;
$direccion = $direccion === "" ? null : $direccion;

try {
    $sql = "UPDATE clientes SET
                nombre = :nombre,
                apellido = :apellido,
                dni = :dni,
                telefono = :telefono,
                email = :email,
                direccion = :direccion
            WHERE id_cliente = :id_cliente";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":dni" => $dni,
        ":telefono" => $telefono,
        ":email" => $email,
        ":direccion" => $direccion,
        ":id_cliente" => $id_cliente
    ]);
    http_response_code(200);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => true,
        "mensaje" => "Cliente actualizado correctamente.",
        "id_cliente" => (int) $id_cliente
    ]);

} catch (PDOException $e) {
    error_log("Error al editar cliente: " . $e->getMessage());

    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "success" => false,
        "mensaje" => "No se pudo actualizar el cliente."
    ], JSON_UNESCAPED_UNICODE);
}
    ?>






