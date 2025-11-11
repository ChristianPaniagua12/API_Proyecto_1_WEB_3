<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once(__DIR__ . "/../modelos/Factura.php");

$factura = new Factura();
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {

        case "GET":
            $op = $_GET["op"] ?? "";
            $cedula = $_GET["cedula"] ?? "";
            $codigo = $_GET["codigo"] ?? "";

            switch ($op) {
                case 'buscar_cliente':
                    $rspta = $factura->buscarCliente($cedula);
                    echo json_encode($rspta ?: ["Error" => "Cliente no encontrado"]);
                    break;

                case 'listar_clientes':
                    $rspta = $factura->listarClientes();
                    $data = [];
                    while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                        $data[] = [
                            $reg->Cedula,
                            $reg->Nombre,
                            $reg->Telefono,
                            $reg->Correo,
                            isset($_GET["select"]) 
                                ? '<button class="btn btn-primary" onclick="selectCliente(\''.$reg->Cedula.'\')"><i class="bx bx-search"></i>&nbsp;Seleccionar</button>'
                                : ''
                        ];
                    }
                    echo json_encode([
                        "sEcho" => 1,
                        "iTotalRecords" => count($data),
                        "iTotalDisplayRecords" => count($data),
                        "aaData" => $data
                    ]);
                    break;

                case 'buscar_producto':
                    $rspta = $factura->buscarProducto($codigo);
                    echo json_encode($rspta ?: ["Error" => "Producto no encontrado"]);
                    break;

                case 'listar_productos':
                    $rspta = $factura->listarProductos();
                    $data = [];
                    while ($reg = $rspta->fetch(PDO::FETCH_OBJ)) {
                        $data[] = [
                            $reg->Codigo,
                            $reg->Nombre,
                            $reg->Precio,
                            $reg->Proveedor ?? "",
                            isset($_GET["select"])
                                ? '<button class="btn btn-primary" onclick="selectProducto(\''.$reg->Codigo.'\')"><i class="bx bx-search"></i>&nbsp;Seleccionar</button>'
                                : ''
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
                    echo json_encode(["Error" => "Operación no válida"]);
                    break;
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