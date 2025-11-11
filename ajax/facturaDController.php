<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once(__DIR__ . "/../modelos/FacturaDetalle.php");

$facturaD = new FacturaDetalle();
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {

        case "POST":
            // Guardar múltiples detalles
            $detalles = $body["detalles"] ?? [];
            
            if (empty($detalles)) {
                echo json_encode(["Error" => "No se enviaron detalles"]);
                break;
            }

            $errores = 0;
            foreach ($detalles as $det) {
                $rspta = $facturaD->insertar(
                    $det['IdEncabezado'] ?? "",
                    $det['CodigoProducto'] ?? "",
                    $det['Cantidad'] ?? 0,
                    $det['PrecioUnitario'] ?? 0
                );
                if (!$rspta) $errores++;
            }

            echo json_encode($errores === 0 
                ? ["Correcto" => "Detalles guardados", "success" => true] 
                : ["Error" => "Algunos detalles no se guardaron", "success" => false]);
            break;

        case "PUT":
            $id = $body["id"] ?? "";
            $codigoProducto = $body["codigoProducto"] ?? "";
            $cantidad = $body["cantidad"] ?? 0;
            $precioUnitario = $body["precioUnitario"] ?? 0;

            $rspta = $facturaD->editar($id, $codigoProducto, $cantidad, $precioUnitario);
            echo json_encode($rspta 
                ? ["Correcto" => "Detalle actualizado"] 
                : ["Error" => "Detalle no se pudo actualizar"]);
            break;

        case "DELETE":
            $id = $body["id"] ?? "";
            $rspta = $facturaD->eliminar($id);
            echo json_encode($rspta 
                ? ["Correcto" => "Detalle eliminado"] 
                : ["Error" => "Detalle no se pudo eliminar"]);
            break;

        case "GET":
            $id = $_GET["id"] ?? "";
            $idEncabezado = $_GET["idEncabezado"] ?? "";

            // Mostrar un detalle específico
            if (!empty($id)) {
                $rspta = $facturaD->mostrar($id);
                echo json_encode($rspta ?: ["Error" => "Detalle no encontrado"]);
                break;
            }

            // Listar detalles por encabezado
            if (!empty($idEncabezado)) {
                $rspta = $facturaD->listarPorEncabezado($idEncabezado);
                $data = [];
                while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                    $data[] = [
                        $reg->Id,
                        $reg->CodigoProducto,
                        $reg->NombreProducto,
                        $reg->Cantidad,
                        $reg->PrecioUnitario,
                        $reg->Subtotal,
                        '<button class="btn btn-warning" onclick="editarDetalle(\''.$reg->Id.'\')"><i class="bx bx-pencil"></i> Editar</button>
                         <button class="btn btn-danger ml-2" onclick="eliminarDetalle(\''.$reg->Id.'\')"><i class="bx bx-trash"></i> Eliminar</button>'
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

            echo json_encode(["Error" => "Parámetros insuficientes"]);
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