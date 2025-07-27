<?php
require 'conexion.php';

// Obtener todos los usuarios
$result = $conexion->query("SELECT id, usuario, password FROM usuarios");

while ($user = $result->fetch_assoc()) {
    // Regenerar hash solo si es necesario
    if (!password_verify('tareafacil25', $user['password'])) {
        $nuevo_hash = password_hash('tareafacil25', PASSWORD_BCRYPT);
        $conexion->query("UPDATE usuarios SET password = '$nuevo_hash' WHERE id = {$user['id']}");
        echo "Reparado usuario {$user['usuario']}<br>";
    }
}

echo "Proceso completado. Todos los hashes son válidos ahora.";
?>