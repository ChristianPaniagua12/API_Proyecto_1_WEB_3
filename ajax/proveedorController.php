<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

require_once("../config/Conexion.php");
require_once("../modelos/Proveedor.php");

$proveedor = new Proveedor();

$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

try {

    switch ($method) {

        case "POST":
            $rspta = $proveedor->insertar($body["codigo"], $body["nombre"], $body["telefono"], $body["correo"], $body["direccion"]);
            if (intval($rspta) == 1) {
                echo json_encode(["Correcto" => "Proveedor agregado"]);
            } elseif (intval($rspta) == 1062) {
                echo json_encode(["Error" => "Código de proveedor repetido"]);
            } else {
                echo json_encode(["Error" => "No se pudo agregar el proveedor."]);
            }
            break;

        case "PUT":
            $rspta = $proveedor->editar($body["codigo"], $body["nombre"], $body["telefono"], $body["correo"], $body["direccion"]);
            echo json_encode($rspta ? ["Correcto" => "Proveedor actualizado"] : ["Error" => "Proveedor no se pudo actualizar"]);
            break;

        case "DELETE":
            $rspta = $proveedor->eliminar($body["codigo"]);
            echo json_encode($rspta ? ["Correcto" => "Proveedor eliminado"] : ["Error" => "Proveedor no se pudo eliminar"]);
            break;

        case "GET":
            $nombre = $_GET["nombre"] ?? ($body["Nombre"] ?? null);
            if (!empty($nombre)) {
                $rspta = $proveedor->buscarPorNombre($nombre);
            } else {
                $rspta = $proveedor->listar();
            }


            $data = [];
            while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                $data[] = [
                    "Codigo" => $reg->Codigo,
                    "Nombre" => $reg->Nombre,
                    "Telefono" => $reg->Telefono,
                    "Correo" => $reg->Correo,
                    "Direccion" => $reg->Direccion
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
?>