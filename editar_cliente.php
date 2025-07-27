<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener ID del cliente a editar
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar formulario de edición
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $direccion = $conexion->real_escape_string($_POST['direccion']);

    $stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $cliente_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Cliente actualizado correctamente";
        header("Location: clientes.php");
        exit();
    } else {
        $error = "Error al actualizar el cliente: " . $stmt->error;
    }
}

// Obtener datos del cliente
$cliente = $conexion->query("SELECT * FROM clientes WHERE id = $cliente_id")->fetch_assoc();

if (!$cliente) {
    $_SESSION['error'] = "Cliente no encontrado";
    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente - La Rubia</title>
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
        .btn-cancelar { background: #dc3545; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Editar Cliente</h2>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Código:</label>
                <input type="text" value="<?php echo htmlspecialchars($cliente['codigo']); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
            </div>
            
            <div class="form-group">
                <label>Dirección:</label>
                <textarea name="direccion" rows="3"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
            </div>
            
            <button type="submit" class="btn">Guardar Cambios</button>
            <a href="clientes.php" class="btn btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>