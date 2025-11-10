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
            $rspta = $cliente->eliminar($body["cedula"] ?? "");
            echo json_encode($rspta ? ["Correcto" => "Cliente eliminado"] : ["Error" => "Cliente no se pudo eliminar"]);
            break;

        case "GET":
            $cedula = isset($_GET["cedula"]) ? trim($_GET["cedula"]) : (isset($body["cedula"]) ? trim($body["cedula"]) : null);

            if (!empty($cedula)) {
                $rspta = $cliente->mostrar($cedula);

                if ($rspta instanceof PDOStatement) {
                    $rspta = $rspta->fetch(PDO::FETCH_ASSOC);
                }

                if ($rspta && is_array($rspta)) {
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
                    "0" => $reg->Cedula,
                    "1" => $reg->Nombre,
                    "2" => $reg->Telefono,
                    "3" => $reg->Correo,
                    "4" =>
                        '<button class="btn btn-warning" onclick="editar(\'' . $reg->Cedula . '\')">
                    <i class="bx bx-pencil"></i>&nbsp;Editar
                </button>
                <button class="btn btn-danger ml-2" onclick="showModal(\'' . $reg->Cedula . '\')">
                    <i class="bx bx-trash"></i>&nbsp;Eliminar
                </button>'
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