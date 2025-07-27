<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verificar si el cliente tiene facturas asociadas
    $tiene_facturas = $conexion->query("SELECT COUNT(*) FROM facturas WHERE cliente_id = $id")->fetch_row()[0];
    
    if ($tiene_facturas > 0) {
        $_SESSION['error'] = "No se puede eliminar el cliente porque tiene facturas asociadas";
    } else {
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Cliente eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar cliente: " . $stmt->error;
        }
    }
}

header("Location: clientes.php");
exit();
?>