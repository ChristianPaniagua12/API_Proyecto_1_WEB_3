<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Codigo");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once(__DIR__ . "/../config/Conexion.php");
    require_once(__DIR__ . "/../modelos/Producto.php");
    require_once(__DIR__ . "/../modelos/Key.php");

    $producto = new Producto();
    $key = new Key();

    $method = $_SERVER['REQUEST_METHOD'];

    $codigo_header = $_SERVER['HTTP_CODIGO'] ?? null;

    if (!$codigo_header) {
        echo json_encode(["Error" => "Acceso no autorizado - Código requerido"]);
        exit();
    }

    $verificacion = $key->VerificarKEY($codigo_header);

    if (empty($verificacion)) {
        $desactivado = $key->VerificarDesactivado($codigo_header);
        echo json_encode([
            "Error" => $desactivado ? "Credenciales desactivadas" : "Acceso no autorizado - Código inválido"
        ]);
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

    $body_encriptado = file_get_contents("php://input");
    $body = [];

    if (!empty($body_encriptado)) {
        $json_desencriptado = Desencriptar_BODY($body_encriptado, $llave);
        $body = json_decode($json_desencriptado, true);

        if ($body === null) {
            echo json_encode(["Error" => "Error al desencriptar los datos"]);
            exit();
        }
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
            } else {
                echo json_encode(["Error" => "No se pudo agregar el producto"]);
            }
            break;

        case "PUT":
            $rspta = $producto->editar($codigo, $nombre, $precio, $codigoProveedor);
            echo json_encode($rspta
                ? ["Correcto" => "Producto actualizado"]
                : ["Error" => "Producto no se pudo actualizar"]
            );
            break;

        case "DELETE":
            $rspta = $producto->eliminar($codigo);
            echo json_encode($rspta
                ? ["Correcto" => "Producto eliminado"]
                : ["Error" => "Producto no se pudo eliminar"]
            );
            break;

        case "GET":
            if (!empty($codigo)) {
                $rspta = $producto->mostrar($codigo);

                if (!empty($rspta) && isset($rspta["Codigo"])) {
                    $data[] = [
                        $rspta["Codigo"],
                        $rspta["Nombre"],
                        $rspta["Precio"],
                        $rspta["CodigoProveedor"] ?? null
                    ];

                    echo json_encode([
                        "sEcho" => 1,
                        "iTotalRecords" => 1,
                        "iTotalDisplayRecords" => 1,
                        "aaData" => $data
                    ]);
                    break;
                }

                echo json_encode(["Error" => "Producto no encontrado"]);
                break;
            }

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

            echo json_encode([
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["Error" => "Método HTTP no permitido"]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "Error" => "Error interno",
        "Mensaje" => $e->getMessage(),
        "Archivo" => $e->getFile(),
        "Linea" => $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        "Error" => "Error fatal",
        "Mensaje" => $e->getMessage(),
        "Archivo" => $e->getFile(),
        "Linea" => $e->getLine()
    ]);
}
?>
