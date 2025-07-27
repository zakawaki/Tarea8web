<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Ventas La Rubia</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
        }
        .welcome-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            width: 90%;
            max-width: 500px;
        }
        .logo {
            font-size: 2.5rem;
            color: #4a6cf7;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-login {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-login:hover {
            background-color: #3a5bd9;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.4);
        }
        .footer {
            margin-top: 30px;
            color: #999;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="logo">La Rubia</div>
        <h1>Sistema de Gestión de Ventas</h1>
        
        <div class="description">
            Sistema diseñado para facilitar el registro de ventas diarias, 
            generación de facturas automáticas y reportes de gestión.
        </div>
        
        <a href="login.php" class="btn-login">Iniciar Sesión</a>
        
        <div class="footer">
            <p>Usuario demo: <strong>demo</strong> / Contraseña: <strong>tareafacil25</strong></p>
        </div>
    </div>
</body>
</html>