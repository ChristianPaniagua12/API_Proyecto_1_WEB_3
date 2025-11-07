<?php
require_once "global.php";

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_ENCODE;
    $conexion = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if (!function_exists('ejecutarConsulta')) {

    function ejecutarConsulta($sql)
    {
        global $conexion;
        $query = $conexion->query($sql);
        return $query;
    }

    function ejecutarConsultaSimpleFila($sql)
    {
        global $conexion;
        $stmt = $conexion->query($sql);
        $row = $stmt->fetch();
        return $row;
    }

    function ejecutarConsulta_retornarID($sql)
    {
        global $conexion;
        $conexion->query($sql);
        return $conexion->lastInsertId();
    }

    function limpiarCadena($str)
    {
        return htmlspecialchars(trim($str));
    }
}
?>
