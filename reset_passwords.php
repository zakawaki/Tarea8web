<?php
require 'conexion.php';

$usuarios = [
    ['demo', 'tareafacil25'],
    ['admin', 'tareafacil25']
];

foreach ($usuarios as $user) {
    $hash = password_hash($user[1], PASSWORD_BCRYPT);
    $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE usuario = ?");
    $stmt->bind_param("ss", $hash, $user[0]);
    $stmt->execute();
    
    echo "Usuario: {$user[0]} - Nuevo hash: $hash<br>";
}
echo "Contraseñas actualizadas correctamente";
?>