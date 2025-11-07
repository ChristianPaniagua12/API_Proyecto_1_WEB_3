<?php
define("DB_HOST", getenv("MYSQLHOST") ?: "mysql.railway.internal"); 
define("DB_PORT", getenv("MYSQLPORT") ?: "3306"); 
define("DB_NAME", getenv("MYSQLDATABASE") ?: "proyecto1web"); 
define("DB_USERNAME", getenv("MYSQLUSER") ?: "root"); 
define("DB_PASSWORD", getenv("MYSQLPASSWORD") ?: getenv("MYSQL_PASS") ?: ""); 
define("DB_ENCODE", "utf8");

define("PRO_NOMBRE", "ITProyecto");
?>
