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

// DEPURACIÓN: Captura el raw input
$rawInput = file_get_contents("php://input");
$body = json_decode($rawInput, true);

// TEMPORAL: Log para ver qué llega
error_log("Raw input: " . $rawInput);
error_log("Body decoded: " . print_r($body, true));

try {
    switch ($method) {

        case "POST":
            // VERIFICA QUE LOS DATOS EXISTEN
            if (empty($body)) {
                echo json_encode([
                    "Error" => "No se recibieron datos", 
                    "raw" => $rawInput,
                    "content_type" => $_SERVER['CONTENT_TYPE'] ?? 'no definido'
                ]);
                break;
            }

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
            $rspta = $cliente->eliminar($body["cedula"] ?? "");
            echo json_encode($rspta ? ["Correcto" => "Cliente eliminado"] : ["Error" => "Cliente no se pudo eliminar"]);
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