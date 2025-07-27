<?php
session_start();
require 'conexion.php';

// Generar número de recibo
$stmt = $conexion->query("SELECT MAX(id) as max_id FROM facturas");
$next_id = $stmt->fetch_assoc()['max_id'] + 1;
$numero_recibo = "REC-" . str_pad($next_id, 3, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nueva Factura - La Rubia</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px;
            box-sizing: border-box;
        }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { 
            padding: 10px 15px; 
            background: #28a745; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-danger { background: #dc3545; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>La Rubia</h2>
            <h3>Nueva Factura</h3>
        </div>
        
        <form id="facturaForm" method="POST" action="guardar_factura.php">
            <div class="form-group">
                <label>Fecha:</label>
                <input type="text" value="<?php echo date('d/m/Y'); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>N° Recibo:</label>
                <input type="text" name="numero_recibo" value="<?php echo $numero_recibo; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Cliente:</label>
                <input type="text" name="cliente_nombre" placeholder="Nombre del cliente" required>
            </div>
            
            <h3>Artículos</h3>
            <table id="productosTable">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th width="100">Cantidad</th>
                        <th width="120">Precio Unitario</th>
                        <th width="120">Total</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="productos[0][descripcion]" placeholder="Descripción del producto" required></td>
                        <td><input type="number" name="productos[0][cantidad]" min="1" value="1" required></td>
                        <td><input type="number" name="productos[0][precio]" min="0.01" step="0.01" placeholder="0.00" required></td>
                        <td class="total-item">RD$0.00</td>
                        <td><button type="button" class="btn-danger" onclick="eliminarProducto(this)">X</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><strong>Total a pagar:</strong></td>
                        <td id="totalPagar">RD$0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <button type="button" class="btn" onclick="agregarProducto()">Agregar Producto</button>
            
            <div class="form-group">
                <label>Comentario:</label>
                <textarea name="comentario" rows="3" placeholder="Observaciones adicionales"></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Guardar Factura</button>
                <button type="button" class="btn-danger" onclick="window.location.href='dashboard.php'">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
        let contadorProductos = 1;
        
        function agregarProducto() {
            const tbody = document.querySelector('#productosTable tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="productos[${contadorProductos}][descripcion]" placeholder="Descripción del producto" required></td>
                <td><input type="number" name="productos[${contadorProductos}][cantidad]" min="1" value="1" required></td>
                <td><input type="number" name="productos[${contadorProductos}][precio]" min="0.01" step="0.01" placeholder="0.00" required></td>
                <td class="total-item">RD$0.00</td>
                <td><button type="button" class="btn-danger" onclick="eliminarProducto(this)">X</button></td>
            `;
            tbody.appendChild(newRow);
            contadorProductos++;
            
            // Agregar eventos a los nuevos inputs
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.type === 'number') {
                    input.addEventListener('input', calcularTotales);
                }
            });
        }
        
        function eliminarProducto(boton) {
            const fila = boton.closest('tr');
            fila.remove();
            calcularTotales();
        }
        
        function calcularTotales() {
            let totalGeneral = 0;
            const filas = document.querySelectorAll('#productosTable tbody tr');
            
            filas.forEach(fila => {
                const cantidad = parseFloat(fila.querySelector('input[name*="cantidad"]').value) || 0;
                const precio = parseFloat(fila.querySelector('input[name*="precio"]').value) || 0;
                const total = cantidad * precio;
                
                fila.querySelector('.total-item').textContent = `RD$${total.toFixed(2)}`;
                totalGeneral += total;
            });
            
            document.getElementById('totalPagar').textContent = `RD$${totalGeneral.toFixed(2)}`;
        }
        
        // Inicializar eventos
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="number"]');
            inputs.forEach(input => {
                input.addEventListener('input', calcularTotales);
            });
        });
    </script>
</body>
</html>