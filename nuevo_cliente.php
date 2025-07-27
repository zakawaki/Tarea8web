<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = 'CLI-' . uniqid();
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    $stmt = $conexion->prepare("INSERT INTO clientes (codigo, nombre, telefono, direccion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $codigo, $nombre, $telefono, $direccion);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Cliente agregado correctamente";
        header("Location: clientes.php");
        exit();
    } else {
        $error = "Error al agregar cliente: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Cliente - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .container { margin-left: 220px; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { 
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
        .btn-volver { background: #6c757d; }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Agregar Nuevo Cliente</h2>
        
        <?php if (isset($error)): ?>
            <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nombre Completo:</label>
                <input type="text" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono">
            </div>
            
            <div class="form-group">
                <label>Dirección:</label>
                <textarea name="direccion" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn">Guardar Cliente</button>
            <a href="clientes.php" class="btn btn-volver">Cancelar</a>
        </form>
    </div>
</body>
</html>