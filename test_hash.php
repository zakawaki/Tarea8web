<?php
$pass = 'tareafacil25';
$hash_correcto = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$hash_actual = '$2y$16992IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro911C/.og/at2.uhew6/jgi';

echo "Comparación con hash correcto: ".(password_verify($pass, $hash_correcto) ? 'OK' : 'Fallo')."<br>";
echo "Comparación con hash actual: ".(password_verify($pass, $hash_actual) ? 'OK' : 'Fallo')."<br>";

// Generar nuevo hash
$nuevo_hash = password_hash($pass, PASSWORD_BCRYPT);
echo "Nuevo hash generado: $nuevo_hash";
?>