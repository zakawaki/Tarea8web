<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$facturas = $conexion->query("
    SELECT f.id, f.numero_recibo, f.fecha, f.total, c.nombre as cliente 
    FROM facturas f
    LEFT JOIN clientes c ON f.cliente_id = c.id
    ORDER BY f.fecha DESC
    LIMIT 50
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Facturas - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .container { margin-left: 220px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 5px 10px; background: #28a745; color: white; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <?php include 'header_sidebar.php'; ?>
    
    <div class="container">
        <h2>Facturas Recientes</h2>
        <table>
            <thead>
                <tr>
                    <th>N° Recibo</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($factura = $facturas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $factura['numero_recibo']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($factura['fecha'])); ?></td>
                    <td><?php echo $factura['cliente'] ?: 'Sin cliente'; ?></td>
                    <td>RD$<?php echo number_format($factura['total'], 2); ?></td>
                    <td>
                        <a href="imprimir_factura.php?id=<?php echo $factura['id']; ?>" class="btn">Ver</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>