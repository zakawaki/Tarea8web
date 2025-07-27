<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$factura_id = $_GET['id'];

// Obtener factura
$stmt = $conexion->prepare("
    SELECT f.*, c.nombre as cliente_nombre, c.codigo as cliente_codigo, u.nombre as usuario_nombre 
    FROM facturas f
    LEFT JOIN clientes c ON f.cliente_id = c.id
    JOIN usuarios u ON f.usuario_id = u.id
    WHERE f.id = ?
");
$stmt->bind_param("i", $factura_id);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();

// Obtener detalles
$stmt = $conexion->prepare("
    SELECT fd.*, p.nombre as producto_nombre, p.codigo as producto_codigo
    FROM factura_detalles fd
    JOIN productos p ON fd.producto_id = p.id
    WHERE fd.factura_id = ?
");
$stmt->bind_param("i", $factura_id);
$stmt->execute();
$detalles = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Factura <?php echo $factura['numero_recibo']; ?> - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .recibo { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        .info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; }
        .footer { margin-top: 30px; text-align: center; font-style: italic; }
        @media print {
            button { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="recibo">
        <div class="header">
            <h2>La Rubia</h2>
            <p>Sistema de Ventas</p>
        </div>
        
        <div class="info">
            <p><strong>N° Recibo:</strong> <?php echo $factura['numero_recibo']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($factura['fecha'])); ?></p>
            <p><strong>Cliente:</strong> <?php echo $factura['cliente_codigo'] . ' - ' . $factura['cliente_nombre']; ?></p>
            <p><strong>Atendido por:</strong> <?php echo $factura['usuario_nombre']; ?></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detalle = $detalles->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $detalle['producto_codigo'] . ' - ' . $detalle['producto_nombre']; ?></td>
                        <td><?php echo $detalle['cantidad']; ?></td>
                        <td>RD$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                        <td>RD$<?php echo number_format($detalle['total'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total">Total a pagar:</td>
                    <td class="total">RD$<?php echo number_format($factura['total'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <?php if (!empty($factura['comentario'])): ?>
            <p><strong>Comentario:</strong> <?php echo $factura['comentario']; ?></p>
        <?php endif; ?>
        
        <div class="footer">
            <p>Gracias por su compra</p>
            <p>Sistema de Ventas La Rubia</p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.location.href='dashboard.php'">Volver</button>
    </div>
</body>
</html>