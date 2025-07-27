<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $existencia = intval($_POST['existencia']);

    $stmt = $conexion->prepare("UPDATE productos SET codigo = ?, nombre = ?, precio = ?, existencia = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $codigo, $nombre, $precio, $existencia, $producto_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Producto actualizado correctamente";
        header("Location: productos.php");
        exit();
    } else {
        $error = "Error al actualizar el producto: " . $stmt->error;
    }
}

$producto = $conexion->query("SELECT * FROM productos WHERE id = $producto_id")->fetch_assoc();

if (!$producto) {
    $_SESSION['error'] = "Producto no encontrado";
    header("Location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Producto - La Rubia</title>
    <style>
        /* Usar los mismos estilos que nuevo_producto.php */
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Editar Producto</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Código:</label>
                <input type="text" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Precio (RD$):</label>
                <input type="number" name="precio" step="0.01" min="0" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Existencia:</label>
                <input type="number" name="existencia" min="0" value="<?php echo htmlspecialchars($producto['existencia']); ?>" required>
            </div>
            
            <button type="submit" class="btn">Guardar Cambios</button>
            <a href="productos.php" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>