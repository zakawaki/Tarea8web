<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: Debes iniciar sesión primero");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validación básica
        if (empty($_POST['numero_recibo']) || empty($_POST['cliente_nombre']) || empty($_POST['productos'])) {
            throw new Exception("Todos los campos son requeridos");
        }

        // Procesar cliente
        $cliente_nombre = trim($_POST['cliente_nombre']);
        $cliente_id = null;

        // Buscar cliente existente
        $stmt_cliente = $conexion->prepare("SELECT id FROM clientes WHERE nombre = ? LIMIT 1");
        $stmt_cliente->bind_param("s", $cliente_nombre);
        $stmt_cliente->execute();
        $result = $stmt_cliente->get_result();

        if ($result->num_rows > 0) {
            $cliente_id = $result->fetch_assoc()['id'];
        } else {
            // Crear nuevo cliente
            $codigo_cliente = 'CLI-' . time();
            $stmt_new_cliente = $conexion->prepare("INSERT INTO clientes (nombre, codigo) VALUES (?, ?)");
            $stmt_new_cliente->bind_param("ss", $cliente_nombre, $codigo_cliente);
            $stmt_new_cliente->execute();
            $cliente_id = $conexion->insert_id;
        }

        // Calcular total
        $total = 0;
        foreach ($_POST['productos'] as $producto) {
            $cantidad = floatval($producto['cantidad']);
            $precio = floatval($producto['precio']);
            $total += $cantidad * $precio;
        }

        // Preparar valores para la factura
        $numero_recibo = $_POST['numero_recibo'];
        $comentario = $_POST['comentario'] ?? '';
        $usuario_id = $_SESSION['user_id'];

        // Insertar factura
        $stmt_factura = $conexion->prepare("INSERT INTO facturas 
                                          (numero_recibo, fecha, cliente_id, total, comentario, usuario_id) 
                                          VALUES (?, NOW(), ?, ?, ?, ?)");
        
        if (!$stmt_factura) {
            throw new Exception("Error al preparar consulta: ".$conexion->error);
        }

        // Asegúrate de que todos los valores sean variables, no literales
        $stmt_factura->bind_param("sidsi", 
            $numero_recibo,
            $cliente_id,
            $total,
            $comentario,
            $usuario_id
        );

        if (!$stmt_factura->execute()) {
            throw new Exception("Error al guardar factura: ".$stmt_factura->error);
        }

        $factura_id = $conexion->insert_id;
        header("Location: imprimir_factura.php?id=$factura_id");
        exit();

    } catch (Exception $e) {
        die("Error: ".$e->getMessage());
    }
}

header("Location: nueva_factura.php");
exit();
?>