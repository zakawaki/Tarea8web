<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener cantidad de facturas recientes
$facturas_recientes = $conexion->query("SELECT COUNT(*) as total FROM facturas WHERE DATE(fecha) = CURDATE()")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .header { background: #343a40; color: white; padding: 15px; display: flex; justify-content: space-between; }
        .sidebar { width: 200px; background: #f8f9fa; height: 100vh; float: left; }
        .content { margin-left: 200px; padding: 20px; }
        .menu-item { padding: 12px 20px; border-bottom: 1px solid #ddd; cursor: pointer; }
        .menu-item:hover { background: #e9ecef; }
        .menu-item a { text-decoration: none; color: #333; display: block; }
        .card { background: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .highlight { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>La Rubia</h2>
        <div>Bienvenido, <?php echo $_SESSION['user_name']; ?> | <a href="logout.php" style="color: white;">Salir</a></div>
    </div>
    
    <div class="sidebar">
        <div class="menu-item"><a href="nueva_factura.php">Nueva Factura</a></div>
        <div class="menu-item"><a href="listar_facturas.php">Ver Facturas</a></div>
        <div class="menu-item"><a href="clientes.php">Clientes</a></div>
        <div class="menu-item"><a href="productos.php">Productos</a></div>
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <div class="menu-item"><a href="reportes.php">Reportes</a></div>
        <?php endif; ?>
    </div>
    
    <div class="content">
        <div class="card">
            <h3>Bienvenido al Sistema de Ventas</h3>
            <p>Facturas hoy: <span class="highlight"><?php echo $facturas_recientes['total']; ?></span></p>
            <p>Seleccione una opción del menú lateral para comenzar.</p>
        </div>
    </div>
</body>
</html>