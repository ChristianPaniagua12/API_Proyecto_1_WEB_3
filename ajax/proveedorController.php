<?php

header("Content-Type: application/json");

require_once("../config/conexion.php");
require_once("../modelos/Proveedor.php");

$proveedor = new Proveedor();

$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    case "POST":
        $rspta = $proveedor->insertar($body["codigo"], $body["nombre"], $body["telefono"], $body["correo"], $body["direccion"]);
        if (intval($rspta) == 1) {
            echo json_encode(["Correcto" => "Proveedor agregado"]);
        }
        if (intval($rspta) == 1062) {
            echo json_encode(["Error" => "Código de proveedor repetido"]);
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
        if (isset($_GET["nombre"]) && !empty($_GET["nombre"])) {
            $nombre = $_GET["nombre"];
            $rspta = $proveedor->buscarPorNombre($nombre);
            $data = array();

            while ($reg = $rspta->fetch_object()) {
                $data[] = array(
                    "0" => $reg->Codigo,
                    "1" => $reg->Nombre,
                    "2" => $reg->Telefono,
                    "3" => $reg->Correo,
                    "4" => $reg->Direccion
                );
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($results);
            break;
        }

        $rspta = $proveedor->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->Codigo,
                "1" => $reg->Nombre,
                "2" => $reg->Telefono,
                "3" => $reg->Correo,
                "4" => $reg->Direccion
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

}
?>