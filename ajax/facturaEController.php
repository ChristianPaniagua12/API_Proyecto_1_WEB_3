<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once(__DIR__ . "/../modelos/FacturaEncabezado.php");

$facturaE = new FacturaEncabezado();
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {

        case "POST":
            $cedulaCliente = $body["cedulaCliente"] ?? "";
            $fecha = $body["fecha"] ?? null;
            $total = $body["total"] ?? "0.00";

            $nuevoId = $facturaE->insertar($cedulaCliente, $fecha, $total);
            echo json_encode($nuevoId > 0 
                ? ["Correcto" => "Factura creada", "Id" => $nuevoId] 
                : ["Error" => "No se pudo crear la factura"]);
            break;

        case "PUT":
            $op = $body["op"] ?? "";
            
            if ($op === "actualizar_total") {
                $id = $body["id"] ?? "";
                $total = $body["total"] ?? "0.00";
                $rspta = $facturaE->actualizarTotal($id, $total);
                echo json_encode($rspta 
                    ? ["Correcto" => "Total actualizado"] 
                    : ["Error" => "No se pudo actualizar el total"]);
            } else {
                $id = $body["id"] ?? "";
                $cedulaCliente = $body["cedulaCliente"] ?? "";
                $fecha = $body["fecha"] ?? "";
                $total = $body["total"] ?? "0.00";
                
                $rspta = $facturaE->editar($id, $cedulaCliente, $fecha, $total);
                echo json_encode($rspta 
                    ? ["Correcto" => "Factura actualizada"] 
                    : ["Error" => "Factura no se pudo actualizar"]);
            }
            break;

        case "DELETE":
            $id = $body["id"] ?? "";
            $rspta = $facturaE->eliminar($id);
            echo json_encode($rspta 
                ? ["Correcto" => "Factura eliminada"] 
                : ["Error" => "Factura no se pudo eliminar"]);
            break;

        case "GET":
            $id = $_GET["id"] ?? "";

            if (!empty($id)) {
                $rspta = $facturaE->mostrar($id);
                echo json_encode($rspta ?: ["Error" => "Factura no encontrada"]);
                break;
            }

            // Listar todas
            $rspta = $facturaE->listar();
            $data = [];
            while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                $data[] = [
                    $reg->Id,
                    $reg->Fecha,
                    $reg->CedulaCliente,
                    $reg->NombreCliente ?? "",
                    $reg->Total,
                    '<button class="btn btn-warning" onclick="editarEncabezado(\''.$reg->Id.'\')"><i class="bx bx-pencil"></i> Editar</button>
                     <button class="btn btn-danger ml-2" onclick="eliminarEncabezado(\''.$reg->Id.'\')"><i class="bx bx-trash"></i> Eliminar</button>'
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