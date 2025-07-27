<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar formulario
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $existencia = intval($_POST['existencia']);

    try {
        $stmt = $conexion->prepare("INSERT INTO productos (codigo, nombre, precio, existencia) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $codigo, $nombre, $precio, $existencia);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Producto agregado correctamente";
            header("Location: productos.php");
            exit();
        } else {
            $error = "Error al agregar el producto: " . $stmt->error;
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Producto - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .container { margin-left: 220px; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn { 
            padding: 8px 15px; 
            background: #28a745; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-cancelar { background: #dc3545; }
        .error { color: red; }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Agregar Nuevo Producto</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Código:</label>
                <input type="text" name="codigo" required>
            </div>
            
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label>Precio (RD$):</label>
                <input type="number" name="precio" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Existencia:</label>
                <input type="number" name="existencia" min="0" value="0" required>
            </div>
            
            <button type="submit" class="btn">Guardar Producto</button>
            <a href="productos.php" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>