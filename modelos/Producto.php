<?php
require "../config/Conexion.php";

class Producto
{
    public function __construct()
    {
    }

    public function insertar($codigo, $nombre, $precio, $codigoProveedor)
    {
        try {
            $sql_check = "SELECT Nombre FROM producto WHERE Nombre = '$nombre' OR Codigo = '$codigo'";
            $res_check = ejecutarConsulta($sql_check);

            if ($res_check->num_rows > 0) {
                return 1062;
            } else {
                $sql = "INSERT INTO producto (Codigo, Nombre, Precio, CodigoProveedor)
                        VALUES ('$codigo', '$nombre', '$precio', '$codigoProveedor')";
                return ejecutarConsulta($sql);
            }
        } catch (Exception $e) {
            return $e->getCode();
        }
    }

    public function editar($codigo, $nombre, $precio, $codigoProveedor)
    {
        $sql = "UPDATE producto SET Nombre='$nombre', Precio='$precio', CodigoProveedor='$codigoProveedor' WHERE Codigo='$codigo'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($codigo)
    {
        $sql = "DELETE FROM producto WHERE Codigo='$codigo'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($codigo)
    {
        $sql = "SELECT * FROM producto WHERE Codigo='$codigo'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function seleccionar($codigo)
    {
        $sql = "SELECT p.Codigo, p.Nombre, p.Precio, pr.Nombre AS Proveedor, pr.Codigo AS CodigoProveedor
                FROM producto p
                JOIN proveedor pr ON p.CodigoProveedor = pr.Codigo
                WHERE p.Codigo='$codigo'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT p.Codigo, p.Nombre, p.Precio, pr.Nombre AS Proveedor
                FROM producto p
                JOIN proveedor pr ON p.CodigoProveedor = pr.Codigo";
        return ejecutarConsulta($sql);
    }

    public function buscar_proveedor($codigoProveedor)
    {
        $sql = "SELECT * FROM proveedor WHERE Codigo = '$codigoProveedor'";
        return ejecutarConsulta($sql);
    }

    public function listar_proveedores()
    {
        $sql = "SELECT * FROM proveedor";
        return ejecutarConsulta($sql);
    }
}
?>
