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
            $cedula = $_GET["cedula"] ?? null;

            if (!empty($cedula)) {
                try {
                    $rspta = $cliente->mostrar($cedula);
                    
                    if (is_array($rspta) && !empty($rspta)) {
                        echo json_encode($rspta);
                    } elseif (is_object($rspta) && method_exists($rspta, 'fetch')) {
                        $data = $rspta->fetch(PDO::FETCH_ASSOC);
                        if ($data) {
                            echo json_encode($data);
                        } else {
                            echo json_encode(["Error" => "Cliente no encontrado"]);
                        }
                    } else {
                        echo json_encode(["Error" => "Cliente no encontrado"]);
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(["Error" => "Error al buscar cliente: " . $e->getMessage()]);
                }
                break;
            }

            try {
                $rspta = $cliente->listar();
                
                if (!$rspta) {
                    echo json_encode([
                        "sEcho" => 1,
                        "iTotalRecords" => 0,
                        "iTotalDisplayRecords" => 0,
                        "aaData" => []
                    ]);
                    break;
                }

                $data = [];
                while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                    $data[] = [
                        "Cedula" => $reg->Cedula,
                        "Nombre" => $reg->Nombre,
                        "Telefono" => $reg->Telefono,
                        "Correo" => $reg->Correo
                    ];
                }

                echo json_encode([
                    "sEcho" => 1,
                    "iTotalRecords" => count($data),
                    "iTotalDisplayRecords" => count($data),
                    "aaData" => $data
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["Error" => "Error al listar clientes: " . $e->getMessage()]);
            }
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