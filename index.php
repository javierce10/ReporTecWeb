<?php
session_start();
// Si ya está logueado, redirigir según rol
if (isset($_SESSION['id_usuario'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo === 'admin') header('Location: vistas/admin.php');
    elseif ($tipo === 'jefe') header('Location: vistas/jefe.php');
    elseif ($tipo === 'profesor') header('Location: vistas/profesor.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReporTec - Instituto Tecnológico de Iguala</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo / Encabezado -->
        <div class="login-header">
            <div class="login-logo">
                <img src="imagenes/logoreportec.png" alt="Logo ReporTec" class="logo-img">
            </div>
            <h1>Bienvenido</h1>
            <p>Instituto Tecnológico de Iguala</p>
        </div>

        <!-- Mensaje de error -->
        <div id="loginError" class="alert alert-danger" style="display:none;"></div>

        <!-- Formulario -->
        <div class="login-form-area">
            <div class="form-group">
                <label>Correo electrónico</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="correo" placeholder="ejemplo@correo.com" autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasena" placeholder="Tu contraseña" autocomplete="current-password">
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button class="btn-login" id="btnLogin" onclick="login()">
                <span id="btnText">Iniciar sesión <i class="fas fa-arrow-right"></i></span>
                <span id="btnLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Cargando...</span>
            </button>
        </div>

        <div class="login-footer">
            <span>TecNM · Campus Iguala</span>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('contrasena');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

async function login() {
    const correo = document.getElementById('correo').value.trim();
    const pass   = document.getElementById('contrasena').value;
    const errDiv = document.getElementById('loginError');
    errDiv.style.display = 'none';

    if (!correo || !pass) {
        errDiv.textContent = 'Por favor completa todos los campos.';
        errDiv.style.display = 'block';
        return;
    }

    document.getElementById('btnText').style.display = 'none';
    document.getElementById('btnLoader').style.display = 'inline';
    document.getElementById('btnLogin').disabled = true;

    try {
        const fd = new FormData();
        fd.append('correo', correo);
        fd.append('contrasena', pass);

        const res  = await fetch('login_web.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.status === 'success') {
            window.location.href = data.redirect;
        } else {
            errDiv.textContent = 'Correo o contraseña incorrectos.';
            errDiv.style.display = 'block';
        }
    } catch (e) {
        errDiv.textContent = 'No se pudo conectar al servidor.';
        errDiv.style.display = 'block';
    } finally {
        document.getElementById('btnText').style.display = 'inline';
        document.getElementById('btnLoader').style.display = 'none';
        document.getElementById('btnLogin').disabled = false;
    }
}

// Enter para login
document.addEventListener('keydown', e => { if (e.key === 'Enter') login(); });
</script>
</body>
</html>
