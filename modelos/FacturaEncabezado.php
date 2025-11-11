<?php
require __DIR__ . "/../config/Conexion.php";

class FacturaEncabezado
{
    public function __construct(){}

    public function insertar($cedulaCliente, $fecha = null, $total = 0.00)
    {
        if ($fecha && trim($fecha) !== '') {
            $sql = "INSERT INTO facturaencabezado (Fecha, CedulaCliente, Total)
                VALUES ('$fecha', '$cedulaCliente', '$total')";
        } else {
            $sql = "INSERT INTO facturaencabezado (CedulaCliente, Total)
                VALUES ('$cedulaCliente', '$total')";
        }
        return ejecutarConsulta_retornarID($sql);
    }

    public function editar($id, $cedulaCliente, $fecha, $total)
    {
        $setFecha = ($fecha && trim($fecha) !== '') ? "Fecha='$fecha'," : "";
        $sql = "UPDATE facturaencabezado
                SET $setFecha CedulaCliente='$cedulaCliente', Total='$total'
                WHERE Id='$id'";
        return ejecutarConsulta($sql);
    }

    public function actualizarTotal($id, $total)
    {
        $sql = "UPDATE facturaencabezado SET Total='$total' WHERE Id='$id'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM facturaencabezado WHERE Id='$id'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($id)
    {
        $sql = "SELECT fe.Id, fe.Fecha, fe.CedulaCliente, c.Nombre AS NombreCliente, fe.Total
                FROM facturaencabezado fe
                LEFT JOIN cliente c ON c.Cedula = fe.CedulaCliente
                WHERE fe.Id='$id'";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function listar()
    {
        $sql = "SELECT fe.Id, fe.Fecha, fe.CedulaCliente, c.Nombre AS NombreCliente, fe.Total
                FROM facturaencabezado fe
                LEFT JOIN cliente c ON c.Cedula = fe.CedulaCliente
                ORDER BY fe.Id DESC";
        return ejecutarConsulta($sql);
    }
}