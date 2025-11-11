<?php
require __DIR__ . "/../config/Conexion.php";

class FacturaDetalle
{
    public function __construct(){}

    public function insertar($idEncabezado, $codigoProducto, $cantidad, $precioUnitario)
    {
        $sql = "INSERT INTO facturadetalle (IdEncabezado, CodigoProducto, Cantidad, PrecioUnitario)
                VALUES ('$idEncabezado', '$codigoProducto', '$cantidad', '$precioUnitario')";
        return ejecutarConsulta($sql);
    }

    public function editar($id, $codigoProducto, $cantidad, $precioUnitario)
    {
        $sql = "UPDATE facturadetalle
                SET CodigoProducto='$codigoProducto', Cantidad='$cantidad', PrecioUnitario='$precioUnitario'
                WHERE Id='$id'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM facturadetalle WHERE Id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT fd.Id, fd.IdEncabezado, fd.CodigoProducto, p.Nombre AS NombreProducto,
                       fd.Cantidad, fd.PrecioUnitario, fd.Subtotal
                FROM facturadetalle fd
                JOIN producto p ON p.Codigo = fd.CodigoProducto
                WHERE fd.Id='$id'";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function listarPorEncabezado($idEncabezado)
    {
        $sql = "SELECT fd.Id, fd.CodigoProducto, p.Nombre AS NombreProducto,
                       fd.Cantidad, fd.PrecioUnitario, fd.Subtotal
                FROM facturadetalle fd
                JOIN producto p ON p.Codigo = fd.CodigoProducto
                WHERE fd.IdEncabezado='$idEncabezado'
                ORDER BY fd.Id ASC";
        return ejecutarConsulta($sql);
    }
}