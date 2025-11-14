<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once(__DIR__ . "/../config/Conexion.php");
require_once(__DIR__ . "/../modelos/Cliente.php");

$cliente = new Cliente();

$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {

        case "POST":
            $rspta = $cliente->insertar(
                $body["cedula"] ?? "",
                $body["nombre"] ?? "",
                $body["telefono"] ?? "",
                $body["correo"] ?? ""
            );

            if ($rspta == 1) {
                echo json_encode(["Correcto" => "Cliente agregado"]);
            } elseif ($rspta == 1062) {
                echo json_encode(["Error" => "Cédula de cliente repetida"]);
            } else {
                echo json_encode(["Error" => "No se pudo agregar el cliente"]);
            }
            break;

        case "PUT":
            $rspta = $cliente->editar(
                $body["cedula"] ?? "",
                $body["nombre"] ?? "",
                $body["telefono"] ?? "",
                $body["correo"] ?? ""
            );
            echo json_encode($rspta ? ["Correcto" => "Cliente actualizado"] : ["Error" => "Cliente no se pudo actualizar"]);
            break;

        case "DELETE":
            $cedula = $body["cedula"] ?? ($_GET["cedula"] ?? null);

            if (empty($cedula)) {
                http_response_code(400);
                echo json_encode(["Error" => "Cédula de cliente no recibida"]);
                break;
            }

            $rspta = $cliente->eliminar($cedula);

            $exito = (
                $rspta === 1 || $rspta === true ||
                (is_object($rspta) && method_exists($rspta, 'rowCount') && $rspta->rowCount() >= 1)
            );

            if ($exito) {
                echo json_encode(["Correcto" => "Cliente eliminado"]);
            } else {
                echo json_encode(["Error" => "Cliente no se pudo eliminar"]);
            }
            break;


        case "GET":
            $cedula = $_GET["cedula"] ?? ($body["cedula"] ?? null);

            if (!empty($cedula)) {
                $rspta = $cliente->mostrar($cedula);
                if (!empty($rspta) && isset($rspta["Cedula"])) {
                    echo json_encode($rspta);
                } else {
                    echo json_encode(["Error" => "Cliente no encontrado"]);
                }
                break;
            }

            $rspta = $cliente->listar();
            $data = [];

            while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                $data[] = [
                    $reg->Cedula,
                    $reg->Nombre,
                    $reg->Telefono,
                    $reg->Correo
                ];
            }

            echo json_encode([
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["Error" => "Método no permitido"]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["Error" => "Error interno: " . $e->getMessage()]);
}
