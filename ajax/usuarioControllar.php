<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once(__DIR__ . "/../modelos/Usuario.php");

$usuario = new Usuario();
$method = $_SERVER['REQUEST_METHOD'];

$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
if (!is_array($body)) { $body = $_POST; }

try {
    switch ($method) {

        case "POST":
            $correo = trim($body["correo"] ?? "");
            $contrasena = $body["contrasena"] ?? "";

            if ($correo === "" || $contrasena === "") {
                echo json_encode(["Error" => "Correo y contraseña son obligatorios"]);
                break;
            }

            $res = $usuario->autenticar($correo, $contrasena);

            if ($res && isset($res["Id"])) {
                echo json_encode([
                    "Correcto" => "Acceso concedido",
                    "usuario"  => $res 
                ]);
            } else {
                echo json_encode(["Error" => "Credenciales inválidas"]);
            }
            break;

        case "GET":
            $correo = $_GET["correo"] ?? "";

            if ($correo !== "") {
                $res = $usuario->mostrarPorCorreo($correo);
                echo json_encode($res ?: ["Error" => "Usuario no encontrado"]);
                break;
            }

            $rspta = $usuario->listar();
            $data = [];
            while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                $data[] = [$reg->Id, $reg->Nombre, $reg->Correo];
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
