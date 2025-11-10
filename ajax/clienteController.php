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
$raw = file_get_contents("php://input");
$body = $raw !== '' ? json_decode($raw, true) : null;
if ($body === null && !empty($_POST)) {
    $body = $_POST;
}

function val($arr, $key) {
    return isset($arr[$key]) ? trim((string)$arr[$key]) : '';
}

try {
    switch ($method) {
        case "POST": {
            if ($body === null) {
                echo json_encode(["Error" => "Body JSON inválido o ausente"]);
                break;
            }
            $cedula   = val($body, "cedula");
            $nombre   = val($body, "nombre");
            $telefono = val($body, "telefono");
            $correo   = val($body, "correo");

            if ($cedula === '' || $nombre === '' || $telefono === '' || $correo === '') {
                echo json_encode(["Error" => "Faltan campos requeridos"]);
                break;
            }

            $rspta = $cliente->insertar($cedula, $nombre, $telefono, $correo);
            if ($rspta == 1) {
                echo json_encode(["Correcto" => "Cliente agregado"]);
            } elseif ($rspta == 1062) {
                echo json_encode(["Error" => "Cédula de cliente repetida"]);
            } else {
                echo json_encode(["Error" => "No se pudo agregar el cliente"]);
            }
            break;
        }

        case "PUT": {
            if ($body === null) {
                echo json_encode(["Error" => "Body JSON inválido o ausente"]);
                break;
            }
            $cedula   = val($body, "cedula");
            $nombre   = val($body, "nombre");
            $telefono = val($body, "telefono");
            $correo   = val($body, "correo");

            if ($cedula === '' || $nombre === '' || $telefono === '' || $correo === '') {
                echo json_encode(["Error" => "Faltan campos requeridos"]);
                break;
            }

            $rspta = $cliente->editar($cedula, $nombre, $telefono, $correo);
            echo json_encode($rspta ? ["Correcto" => "Cliente actualizado"] : ["Error" => "Cliente no se pudo actualizar"]);
            break;
        }

        case "DELETE": {
            if ($body === null) {
                echo json_encode(["Error" => "Body JSON inválido o ausente"]);
                break;
            }
            $cedula = val($body, "cedula");
            if ($cedula === '') {
                echo json_encode(["Error" => "Cédula requerida"]);
                break;
            }
            $rspta = $cliente->eliminar($cedula);
            echo json_encode($rspta ? ["Correcto" => "Cliente eliminado"] : ["Error" => "Cliente no se pudo eliminar"]);
            break;
        }

        case "GET": {
            $cedula = isset($_GET["cedula"]) ? trim((string)$_GET["cedula"]) : '';
            if ($cedula === '' && is_array($body)) {
                $cedula = val($body, "cedula"); 
            }

            if ($cedula !== '') {
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
        }

        default:
            http_response_code(405);
            echo json_encode(["Error" => "Método no permitido"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["Error" => "Error interno: " . $e->getMessage()]);
}
