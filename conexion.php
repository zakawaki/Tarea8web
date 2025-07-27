<?php
$host = "localhost";
$usuario = "root";
$password = ""; // Vacío por defecto en XAMPP
$base_datos = "la_rubia";

$conexion = new mysqli($host, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Forzar UTF-8 y modo estricto
$conexion->set_charset("utf8mb4");
$conexion->query("SET SQL_MODE = 'STRICT_ALL_TABLES'");
?>