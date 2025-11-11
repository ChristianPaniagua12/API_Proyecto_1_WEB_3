<?php
require __DIR__ . "/../config/Conexion.php";

class Usuario
{
    public function __construct(){}

    public function autenticar($correo, $contrasena)
    {
        $sql = "SELECT Id, Nombre, Correo
                FROM usuario
                WHERE Correo = '$correo'
                  AND Contrasena = SHA2('$contrasena', 256)
                LIMIT 1";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function mostrarPorCorreo($correo)
    {
        $sql = "SELECT Id, Nombre, Correo
                FROM usuario
                WHERE Correo = '$correo'
                LIMIT 1";
        $res = ejecutarConsulta($sql);
        return $res ? $res->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function listar()
    {
        $sql = "SELECT Id, Nombre, Correo
                FROM usuario
                ORDER BY Id DESC";
        return ejecutarConsulta($sql);
    }
}
