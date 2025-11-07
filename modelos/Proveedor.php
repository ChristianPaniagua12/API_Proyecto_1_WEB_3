<?php

require "../config/Conexion.php";

class Proveedor
{
    public function __construct()
    {

    }

    public function insertar($codigo, $nombre, $telefono, $correo, $direccion)
    {
        try {
            $sql = "INSERT INTO proveedor (Codigo, Nombre, Telefono, Correo, Direccion)
                    VALUES ('$codigo', '$nombre', '$telefono', '$correo', '$direccion')";
            return ejecutarConsulta($sql);
        } catch (Exception $e) {
            return $e->getCode();
        }
    }

    public function editar($codigo, $nombre, $telefono, $correo, $direccion)
    {
        $sql = "UPDATE proveedor 
                SET Nombre='$nombre', Telefono='$telefono', Correo='$correo', Direccion='$direccion' 
                WHERE Codigo='$codigo'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($codigo)
    {
        $sql = "DELETE FROM proveedor WHERE Codigo='$codigo'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($nombre)
    {
        $sql = "SELECT * FROM proveedor WHERE Nombre='$nombre'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM proveedor";
        return ejecutarConsulta($sql);
    }

    public function buscarPorNombre($nombre)
    {
        $sql = "SELECT * FROM proveedor WHERE Nombre LIKE '%$nombre%'";
        return ejecutarConsulta($sql);
    }

}
?>