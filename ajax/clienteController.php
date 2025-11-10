<?php

require_once "../modelos/Cliente.php";

$cliente = new Cliente();

$cedula   = isset($_POST["cedula"]) ? $_POST["cedula"] : "";
$nombre   = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
$telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : "";
$correo   = isset($_POST["correo"]) ? $_POST["correo"] : "";

switch ($_GET["op"]) {
    case 'guardar':
        $rspta = $cliente->insertar($cedula, $nombre, $telefono, $correo);
        if (intval($rspta) == 1) {
            echo "Cliente agregado";
        }
        if (intval($rspta) == 1062) {
            echo "Cédula de cliente repetida";
        }
        break;

    case 'editar':
        $rspta = $cliente->editar($cedula, $nombre, $telefono, $correo);
        echo $rspta ? "Cliente actualizado" : "Cliente no se pudo actualizar";
        break;

    case 'eliminar':
        $rspta = $cliente->eliminar($cedula);
        echo $rspta ? "Cliente eliminado" : "Cliente no se pudo eliminar";
        break;

    case 'mostrar':
        $rspta = $cliente->mostrar($cedula);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $cliente->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->Cedula,
                "1" => $reg->Nombre,
                "2" => $reg->Telefono,
                "3" => $reg->Correo,
                "4" =>
                    '<button class="btn btn-warning" onclick="editar(\'' . $reg->Cedula . '\')">
                        <i class="bx bx-pencil"></i>&nbsp;Editar
                    </button>
                    <button class="btn btn-danger ml-2" onclick="showModal(\'' . $reg->Cedula . '\')">
                        <i class="bx bx-trash"></i>&nbsp;Eliminar
                    </button>'
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
