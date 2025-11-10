<?php 

require "../config/Conexion.php";

Class Cliente
{
    public function __construct()
    {

    }

    public function insertar($cedula, $nombre, $telefono, $correo)
    {
        try {
            $sql = "INSERT INTO cliente (Cedula, Nombre, Telefono, Correo)
                    VALUES ('$cedula', '$nombre', '$telefono', '$correo')";
            return ejecutarConsulta($sql);
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
