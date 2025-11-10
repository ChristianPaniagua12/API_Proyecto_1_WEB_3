<?php 
require __DIR__ . "/../config/Conexion.php";

class Cliente
{
    public function __construct() {}

    public function insertar($cedula, $nombre, $telefono, $correo)
    {
        try {
           
            $sql_check = "SELECT 1 FROM cliente WHERE Cedula = '$cedula' LIMIT 1";
            $res_check = ejecutarConsulta($sql_check);

            if ($res_check && $res_check->fetch(PDO::FETCH_NUM)) {
                return 1062;
            }

            $sql = "INSERT INTO cliente (Cedula, Nombre, Telefono, Correo)
                    VALUES ('$cedula', '$nombre', '$telefono', '$correo')";
            $ok = ejecutarConsulta($sql);

            return $ok ? 1 : 0;
        } catch (Exception $e) {
            return $e->getCode();
        }
    }

    public function editar($cedula, $nombre, $telefono, $correo)
    {
        $sql = "UPDATE cliente 
                SET Nombre='$nombre', Telefono='$telefono', Correo='$correo' 
                WHERE Cedula='$cedula'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($cedula)
    {   
        $sql = "DELETE FROM cliente WHERE Cedula='$cedula'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($cedula)
    {
        $sql = "SELECT * FROM cliente WHERE Cedula='$cedula'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM cliente";
        return ejecutarConsulta($sql);        
    }
}
