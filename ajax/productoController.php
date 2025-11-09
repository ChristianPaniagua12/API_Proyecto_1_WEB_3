<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Codigo");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../modelos/Producto.php";
require_once "../modelos/Key.php";

$producto = new Producto();
$key = new Key();

$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();
if (!isset($headers['Codigo'])) {
    echo json_encode(["Error" => "Acceso no autorizado - Código requerido"]);
    exit();
}

$verificacion = $key->VerificarKEY($headers['Codigo']);

if (empty($verificacion)) {
    $desactivado = $key->VerificarDesactivado($headers['Codigo']);
    if ($desactivado) {
        echo json_encode(["Error" => "Credenciales desactivadas"]);
    } else {
        echo json_encode(["Error" => "Acceso no autorizado - Código inválido"]);
    }
    exit();
}

$llave = $verificacion[0]['Key'];

function Desencriptar_BODY($json, $llave)
{
    $cifrado = "aes-256-ecb";
    $json_desencriptado = openssl_decrypt(
        base64_decode($json),
        $cifrado,
        $llave,
        OPENSSL_RAW_DATA
    );
    if ($json_desencriptado === false) {
        return false;
    }
    return $json_desencriptado;
}

if ($method !== "GET") {
    $body_encriptado = file_get_contents("php://input");
    $body = json_decode(Desencriptar_BODY($body_encriptado, $llave), true);

    if ($body === null) {
        echo json_encode(["Error" => "Error al desencriptar los datos"]);
        exit();
    }
} else {
    $body = [];
}

$codigo = $body["codigo"] ?? ($_GET["codigo"] ?? "");
$nombre = $body["nombre"] ?? "";
$precio = $body["precio"] ?? "";
$codigoProveedor = $body["codigoProveedor"] ?? "";


switch ($method) {

    case "POST":
        $rspta = $producto->insertar($codigo, $nombre, $precio, $codigoProveedor);
        if (intval($rspta) == 1) {
            echo json_encode(["Correcto" => "Producto agregado"]);
        } elseif (intval($rspta) == 1062) {
            echo json_encode(["Error" => "Producto ya existe"]);
        }
        break;

    case "PUT":
        $rspta = $producto->editar($codigo, $nombre, $precio, $codigoProveedor);
        echo json_encode($rspta ? ["Correcto" => "Producto actualizado"] : ["Error" => "Producto no se pudo actualizar"]);
        break;

    case "DELETE":
        $rspta = $producto->eliminar($codigo);
        echo json_encode($rspta ? ["Correcto" => "Producto eliminado"] : ["Error" => "Producto no se pudo eliminar"]);
        break;

    case "GET":
        // Si viene código por query string
        if (isset($_GET["codigo"]) && !empty($_GET["codigo"])) {
            $rspta = $producto->mostrar($_GET["codigo"]);
            echo json_encode($rspta);
            break;
        }

        // Si no viene código, listamos todos (con body encriptado)
        $body_encriptado = file_get_contents("php://input");

        if (!empty($body_encriptado)) {
            $body = json_decode(Desencriptar_BODY($body_encriptado, $llave), true);

            if ($body === null) {
                echo json_encode(["Error" => "Error al desencriptar los datos"]);
                exit();
            }
        } else {
            $body = [];
        }

        // Listar todos
        $rspta = $producto->listar();
        $data = [];

        while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
            $data[] = [
                "0" => $reg->Codigo,
                "1" => $reg->Nombre,
                "2" => $reg->Precio,
                "3" => $reg->Proveedor
            ];
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];
        echo json_encode($results);
        break;


    default:
        echo json_encode(["Error" => "Método HTTP no permitido"]);
}
?>