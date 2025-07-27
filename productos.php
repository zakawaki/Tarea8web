<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener lista de productos
$productos = $conexion->query("SELECT id, codigo, nombre, precio, existencia FROM productos ORDER BY nombre");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Productos - La Rubia</title>
    <style>
        /* Usar los mismos estilos que clientes.php */
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
        .precio { text-align: right; }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Inventario de Productos</h2>
        <a href="nuevo_producto.php" class="agregar-btn">Agregar Nuevo Producto</a>
        
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Existencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($productos->num_rows > 0): ?>
                    <?php while($producto = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td class="precio">RD$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($producto['existencia']); ?></td>
                        <td>
                            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-editar">Editar</a>
                            <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay productos registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="container">
    <h2>Inventario de Productos</h2>
    
    <div style="margin-bottom: 20px;">
        <a href="nuevo_producto.php" class="agregar-btn">Agregar Nuevo Producto</a>
        <a href="dashboard.php" class="btn" style="background: #6c757d; margin-left: 10px;">
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
    </div>
    
    <!-- Resto de tu tabla de productos -->
</div>
</body>
</html>