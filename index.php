<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ReporTec - Iniciar Sesión</title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>ReporTec</h1>
            <p>Gestión de Incidencias Universitarias</p>
            <form action="procesologin.php" method="POST">
                <div class="input-group">
                    <label>Correo Institucional</label>
                    <input type="email" name="correo" required placeholder="usuario@iguala.tecnm.mx">
                </div>
                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-login">Ingresar</button>
            </form>
            <?php if(isset($_GET['error'])) echo '<p class="error">Credenciales incorrectas</p>'; ?>
        </div>
    </div>
</body>
</html>