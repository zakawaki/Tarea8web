<?php
// Nivel máximo de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si ya está instalado
define('CONFIG_FILE', 'config.php');
if (file_exists(CONFIG_FILE)) {
    die('<h2>El sistema ya está instalado</h2>
         <p>Para reinstalar, elimina el archivo '.CONFIG_FILE.' primero.</p>
         <a href="index.php">Ir al sistema</a>');
}

// Procesar el formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_name = $_POST['db_name'] ?? 'la_rubia';
    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? '';
    $admin_email = $_POST['admin_email'] ?? 'admin@larubia.com';
    
    try {
        // 1. Conectar al servidor MySQL
        $conn = new mysqli($db_host, $db_user, $db_pass);
        
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }
        
        // 2. Crear la base de datos
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            throw new Exception("Error al crear la base de datos: " . $conn->error);
        }
        
        // 3. Seleccionar la base de datos
        $conn->select_db($db_name);
        
        // 4. Crear las tablas
        $sql = "
        SET FOREIGN_KEY_CHECKS = 0;
        
        DROP TABLE IF EXISTS usuarios;
        CREATE TABLE usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            rol ENUM('admin','vendedor') NOT NULL DEFAULT 'vendedor',
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
        
        DROP TABLE IF EXISTS clientes;
        CREATE TABLE clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(20) NOT NULL UNIQUE,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20),
            direccion TEXT,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
        
        DROP TABLE IF EXISTS productos;
        CREATE TABLE productos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codigo VARCHAR(20) NOT NULL UNIQUE,
            nombre VARCHAR(100) NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            existencia INT NOT NULL DEFAULT 0,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
        
        DROP TABLE IF EXISTS facturas;
        CREATE TABLE facturas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_recibo VARCHAR(20) NOT NULL UNIQUE,
            fecha DATETIME NOT NULL,
            cliente_id INT NULL,
            total DECIMAL(10,2) NOT NULL,
            comentario TEXT,
            usuario_id INT NOT NULL,
            FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ) ENGINE=InnoDB;
        
        DROP TABLE IF EXISTS factura_detalles;
        CREATE TABLE factura_detalles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            factura_id INT NOT NULL,
            producto_id INT NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
            FOREIGN KEY (producto_id) REFERENCES productos(id)
        ) ENGINE=InnoDB;
        
        SET FOREIGN_KEY_CHECKS = 1;
        ";
        
        // Ejecutar las consultas SQL
        if ($conn->multi_query($sql)) {
            // Vaciar resultados múltiples
            while ($conn->more_results()) {
                $conn->next_result();
            }
        } else {
            throw new Exception("Error al crear tablas: " . $conn->error);
        }
        
        // 5. Crear usuario administrador
        $hashed_password = password_hash($admin_pass, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, nombre, email, rol) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->bind_param("ssss", $admin_user, $hashed_password, $admin_user, $admin_email);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al crear usuario admin: " . $stmt->error);
        }
        
        // 6. Crear archivo de configuración
        $config_content = "<?php
/**
 * Configuración de la base de datos - Generado automáticamente
 */
define('DB_HOST', '".addslashes($db_host)."');
define('DB_USER', '".addslashes($db_user)."');
define('DB_PASS', '".addslashes($db_pass)."');
define('DB_NAME', '".addslashes($db_name)."');

// Configuración del sistema
define('SISTEMA_INSTALADO', true);
define('VERSION', '1.0.0');

// Configuración de seguridad
define('HASH_KEY', '".bin2hex(random_bytes(32))."');
";
        
        if (file_put_contents(CONFIG_FILE, $config_content) === false) {
            throw new Exception("No se pudo crear el archivo de configuración. Verifica permisos.");
        }
        
        // 7. Mostrar mensaje de éxito
        $success = true;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - Sistema La Rubia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border 0.3s;
        }
        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52,152,219,0.5);
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            border-left: 4px solid #3498db;
        }
        .requirements h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .requirements ul {
            padding-left: 20px;
        }
        .requirements li {
            margin-bottom: 8px;
        }
        .success-box {
            text-align: center;
            padding: 30px;
        }
        .success-box a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalación del Sistema La Rubia</h1>
        
        <?php if (isset($success)): ?>
            <div class="success-box">
                <h2>¡Instalación completada con éxito!</h2>
                <p>El sistema se ha instalado correctamente.</p>
                <p><strong>Usuario administrador:</strong> <?php echo htmlspecialchars($admin_user); ?></p>
                <a href="login.php">Continuar al Login</a>
            </div>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="requirements">
                <h3>Requisitos del sistema:</h3>
                <ul>
                    <li>PHP 7.4 o superior</li>
                    <li>MySQL 5.7 o MariaDB 10.2+</li>
                    <li>Extensiones PHP: mysqli, pdo_mysql</li>
                    <li>Permisos de escritura en el directorio</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <h3>Configuración de la base de datos</h3>
                
                <div class="form-group">
                    <label for="db_host">Servidor MySQL:</label>
                    <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_user">Usuario MySQL:</label>
                    <input type="text" id="db_user" name="db_user" value="<?php echo htmlspecialchars($_POST['db_user'] ?? 'root'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="db_pass">Contraseña MySQL:</label>
                    <input type="password" id="db_pass" name="db_pass" value="<?php echo htmlspecialchars($_POST['db_pass'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="db_name">Nombre de la base de datos:</label>
                    <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'la_rubia'); ?>" required>
                </div>
                
                <h3>Cuenta de administrador</h3>
                
                <div class="form-group">
                    <label for="admin_user">Nombre de usuario admin:</label>
                    <input type="text" id="admin_user" name="admin_user" value="<?php echo htmlspecialchars($_POST['admin_user'] ?? 'admin'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_pass">Contraseña admin:</label>
                    <input type="password" id="admin_pass" name="admin_pass" value="<?php echo htmlspecialchars($_POST['admin_pass'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Email admin:</label>
                    <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($_POST['admin_email'] ?? 'admin@larubia.com'); ?>" required>
                </div>
                
                <button type="submit">Instalar Sistema</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>