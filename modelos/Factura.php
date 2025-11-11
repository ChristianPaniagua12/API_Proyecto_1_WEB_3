<?php
require __DIR__ . "/../config/Conexion.php";

class Factura
{
    public function __construct(){}

    public function buscarCliente($cedula)
    {
        $sql = "SELECT Cedula, Nombre, Telefono, Correo
                FROM cliente
                WHERE Cedula = '$cedula'";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function listarClientes()
    {
        $sql = "SELECT Cedula, Nombre, Telefono, Correo
                FROM cliente
                ORDER BY Nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function buscarProducto($codigo)
    {
        $sql = "SELECT p.Codigo, p.Nombre, p.Precio, p.CodigoProveedor,
                       pr.Nombre AS Proveedor
                FROM producto p
                LEFT JOIN proveedor pr ON pr.Codigo = p.CodigoProveedor
                WHERE p.Codigo = '$codigo'";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function listarProductos()
    {
        $sql = "SELECT p.Codigo, p.Nombre, p.Precio,
                       pr.Nombre AS Proveedor
                FROM producto p
                LEFT JOIN proveedor pr ON pr.Codigo = p.CodigoProveedor
                ORDER BY p.Codigo ASC";
        return ejecutarConsulta($sql);
    }
}