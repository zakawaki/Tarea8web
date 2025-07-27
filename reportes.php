<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Obtener resumen del día
$stmt = $conexion->prepare("
    SELECT COUNT(*) as total_facturas, SUM(total) as total_ventas
    FROM facturas
    WHERE DATE(fecha) = ?
");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$resumen = $stmt->get_result()->fetch_assoc();

// Obtener facturas del día
$stmt = $conexion->prepare("
    SELECT f.id, f.numero_recibo, f.fecha, f.total, c.nombre as cliente_nombre
    FROM facturas f
    LEFT JOIN clientes c ON f.cliente_id = c.id
    WHERE DATE(f.fecha) = ?
    ORDER BY f.fecha DESC
");
$stmt->bind_param("s", $fecha);
$stmt->execute();
$facturas = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reporte Diario - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #218838; }
        .form-group { margin-bottom: 15px; }
        .resumen { display: flex; justify-content: space-around; text-align: center; margin-bottom: 20px; }
        .resumen-item { padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .resumen-valor { font-size: 1.5em; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reporte Diario</h2>
        
        <div class="card">
            <form method="GET">
                <div class="form-group">
                    <label>Seleccione fecha:</label>
                    <input type="date" name="fecha" value="<?php echo $fecha; ?>">
                    <button type="submit" class="btn">Consultar</button>
                </div>
            </form>
            
            <div class="resumen">
                <div class="resumen-item">
                    <div class="resumen-titulo">Facturas</div>
                    <div class="resumen-valor"><?php echo $resumen['total_facturas']; ?></div>
                </div>
                <div class="resumen-item">
                    <div class="resumen-titulo">Total Ventas</div>
                    <div class="resumen-valor">RD$<?php echo number_format($resumen['total_ventas'], 2); ?></div>
                </div>
            </div>
            
            <h3>Detalle de Facturas</h3>
            <table>
                <thead>
                    <tr>
                        <th>N° Recibo</th>
                        <th>Fecha/Hora</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($factura = $facturas->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $factura['numero_recibo']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($factura['fecha'])); ?></td>
                            <td><?php echo $factura['cliente_nombre'] ?: 'Sin cliente'; ?></td>
                            <td>RD$<?php echo number_format($factura['total'], 2); ?></td>
                            <td>
                                <a href="imprimir_factura.php?id=<?php echo $factura['id']; ?>">Ver</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>