<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener lista de clientes
$clientes = $conexion->query("SELECT id, codigo, nombre, telefono FROM clientes ORDER BY nombre");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Clientes - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .container { margin-left: 220px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn { 
            padding: 5px 10px; 
            background: #28a745; 
            color: white; 
            text-decoration: none; 
            border-radius: 3px;
            margin-right: 5px;
        }
        .btn-editar { background: #17a2b8; }
        .btn-eliminar { background: #dc3545; }
        .agregar-btn { 
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Listado de Clientes</h2>
        <a href="nuevo_cliente.php" class="agregar-btn">Agregar Nuevo Cliente</a>
        
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($clientes->num_rows > 0): ?>
                    <?php while($cliente = $clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                        <td>
                            <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-editar">Editar</a>
                            <a href="eliminar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar este cliente?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay clientes registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="container">
    <h2>Listado de Clientes</h2>
    
    <div style="margin-bottom: 20px;">
        <a href="nuevo_cliente.php" class="agregar-btn">Agregar Nuevo Cliente</a>
        <a href="dashboard.php" class="btn" style="background: #6c757d; margin-left: 10px;">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
    </div>
    
    <!-- Resto de tu tabla de clientes -->
</div>
</body>
</html>