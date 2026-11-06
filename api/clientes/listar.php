<?php
require_once __DIR__ . "/../../database.php";
$estado = strtolower((string)($_GET['estado'] ?? 'activos'));

//solo activos
$sql = "SELECT id_cliente, nombre, apellido, dni, telefono, email, direccion, activo, fecha_registro
        FROM clientes
        WHERE activo = 1
        ORDER BY id_cliente DESC";
        switch ($estado) {
            case "inactivos":
                $sql = "SELECT id_cliente, nombre, apellido, dni, telefono, email, direccion, activo, fecha_registro
                        FROM clientes
                        WHERE activo = 0
                        ORDER BY id_cliente DESC";
                break;
            case "todos":
                $sql = "SELECT id_cliente, nombre, apellido, dni, telefono, email, direccion, activo, fecha_registro
                        FROM clientes
                        ORDER BY id_cliente DESC";
                break;
            default:
                $sql = "SELECT id_cliente, nombre, apellido, dni, telefono, email, direccion, activo, fecha_registro
                        FROM clientes
                        WHERE activo = 1
                        ORDER BY id_cliente DESC";
                break;
        }
        try {
            $stmt = $conexion->query($sql);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success"  => true,
                "total"    => count($clientes),
                "clientes" => $clientes
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            error_log("Error al listar clientes: " . $e->getMessage());

            http_response_code(500);
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode([
                "success" => false,
                "mensaje" => "No se pudieron obtener los clientes."
            ], JSON_UNESCAPED_UNICODE);
        }
        ?>












