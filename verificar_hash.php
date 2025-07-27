<?php
require 'conexion.php';

$usuario = 'demo';
$password = 'tareafacil25';

$hash = $conexion->query("SELECT password FROM usuarios WHERE usuario = '$usuario'")->fetch_row()[0];

echo "Hash almacenado: $hash<br>";
echo "Longitud: " . strlen($hash) . "<br>";
echo "Verificación: " . (password_verify($password, $hash) ? 'ÉXITO' : 'FALLO');
?>