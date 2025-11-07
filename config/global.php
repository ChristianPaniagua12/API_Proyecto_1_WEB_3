<?php
$databaseUrl = getenv("MYSQL_URL") ?: getenv("MYSQL_PUBLIC_URL");

if ($databaseUrl) {
    $parsedUrl = parse_url($databaseUrl);
    define("DB_HOST", $parsedUrl["host"]);
    define("DB_PORT", $parsedUrl["port"]);
    define("DB_NAME", ltrim($parsedUrl["path"], "/"));
    define("DB_USERNAME", $parsedUrl["user"]);
    define("DB_PASSWORD", $parsedUrl["pass"]);
} else {
    define("DB_HOST", getenv("MYSQLHOST") ?: "mysql.railway.internal");
    define("DB_PORT", getenv("MYSQLPORT") ?: "3306");
    define("DB_NAME", getenv("MYSQLDATABASE") ?: "proyecto1web");
    define("DB_USERNAME", getenv("MYSQLUSER") ?: "root");
    define("DB_PASSWORD", getenv("MYSQLPASSWORD") ?: getenv("MYSQL_ROOT_PASSWORD") ?: "");
}

define("DB_ENCODE", "utf8mb4");
define("PRO_NOMBRE", "ITProyecto");
?>
