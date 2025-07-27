<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verificar si el producto está en alguna factura
    $en_facturas = $conexion->query("SELECT COUNT(*) FROM factura_detalles WHERE producto_id = $id")->fetch_row()[0];
    
    if ($en_facturas > 0) {
        $_SESSION['error'] = "No se puede eliminar el producto porque está en facturas existentes";
    } else {
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Producto eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar producto: " . $stmt->error;
        }
    }
}

header("Location: productos.php");
exit();
?>