<?php
require "../config/Conexion.php";

class Key
{
    public function __construct()
    {
    }

    public function VerificarKEY($codigo)
    {
        $sql = "SELECT * FROM api WHERE Codigo = '$codigo' AND Status = 1";
        $query = ejecutarConsulta($sql);
        $resultado = array();

        while ($fila = $query->fetch_assoc()) {
            $resultado[] = $fila;
        }

        return $resultado;
    }

    public function VerificarDesactivado($codigo)
    {
        $sql = "SELECT * FROM api WHERE Codigo = '$codigo' AND Status = 0";
        $query = ejecutarConsulta($sql);
        return $query->fetch_assoc();
    }
}
?>
