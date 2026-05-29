<?php
require_once '../auth_check.php';
$home = ($tipo === 'admin') ? 'admin.php' : (($tipo === 'jefe') ? 'jefe.php' : 'profesor.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil – ReporTec</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="icon"><i class="fas fa-shield-alt" style="color:white"></i></div>
                <span>ReporTec</span>
            </div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inicial ?></div>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <small><?= ucfirst($tipo) ?></small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a class="nav-item" href="<?= $home ?>"><i class="fas fa-home"></i> Inicio</a>
            <a class="nav-item active" href="perfil.php"><i class="fas fa-user-circle"></i> Mi perfil</a>
        </nav>
        <div class="sidebar-footer">
            <button class="btn-logout" onclick="location.href='../logout.php'">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </button>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <span class="topbar-title">Mi Perfil</span>
            <a href="<?= $home ?>" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div class="page-body anim" style="max-width:600px;margin:0 auto">

            <!-- Avatar card -->
            <div class="form-card" style="text-align:center;margin-bottom:20px">
                <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                    display:flex;align-items:center;justify-content:center;font-size:36px;color:white;
                    font-weight:800;margin:0 auto 16px"><?= $inicial ?></div>
                <div style="font-size:20px;font-weight:700"><?= htmlspecialchars($nombre) ?></div>
                <div style="color:var(--gray);font-size:13px;margin:4px 0"><?= htmlspecialchars($correo) ?></div>
                <span class="badge badge-admin" style="margin-top:8px"><?= ucfirst($tipo) ?></span>
            </div>

            <!-- Info -->
            <div class="form-card" style="margin-bottom:20px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                    <i class="fas fa-user" style="color:var(--primary)"></i>
                    <strong>Información general</strong>
                </div>
                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:16px">
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="display:flex;justify-content:space-between;font-size:13px">
                        <span style="color:var(--gray)">Nombre</span>
                        <span style="font-weight:600"><?= htmlspecialchars($nombre) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13px">
                        <span style="color:var(--gray)">Correo</span>
                        <span style="font-weight:600"><?= htmlspecialchars($correo) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13px">
                        <span style="color:var(--gray)">Tipo de usuario</span>
                        <span style="font-weight:600"><?= ucfirst($tipo) ?></span>
                    </div>
                </div>
            </div>

            <!-- Config -->
            <div class="form-card">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                    <i class="fas fa-cog" style="color:var(--primary)"></i>
                    <strong>Configuración</strong>
                </div>
                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:16px">

                <button class="btn btn-outline" style="width:100%;justify-content:flex-start;margin-bottom:10px"
                    onclick="openModal('modalContrasena')">
                    <i class="fas fa-lock" style="color:var(--primary)"></i> Cambiar contraseña
                    <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--gray)"></i>
                </button>

                <button class="btn btn-danger" style="width:100%;justify-content:flex-start"
                    onclick="if(confirm('¿Cerrar sesión?')) location.href='../logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    <i class="fas fa-chevron-right" style="margin-left:auto"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cambiar contraseña -->
<div class="modal-overlay" id="modalContrasena">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <h3>Cambiar contraseña</h3>
            <button class="modal-close" onclick="closeModal('modalContrasena')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="passAlert"></div>
            <div class="form-row" style="margin-bottom:0">
                <div class="field">
                    <label>Contraseña actual</label>
                    <input type="password" id="passActual" placeholder="Tu contraseña actual">
                </div>
                <div class="field">
                    <label>Nueva contraseña</label>
                    <input type="password" id="passNueva" placeholder="Nueva contraseña">
                </div>
                <div class="field">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" id="passConfirmar" placeholder="Repite la nueva contraseña">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalContrasena')">Cancelar</button>
            <button class="btn btn-primary" onclick="cambiarContrasena()">Guardar</button>
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
<script>
async function cambiarContrasena() {
    const actual    = document.getElementById('passActual').value;
    const nueva     = document.getElementById('passNueva').value;
    const confirmar = document.getElementById('passConfirmar').value;
    const alert     = document.getElementById('passAlert');
    alert.innerHTML = '';

    if (!actual || !nueva || !confirmar) {
        alert.innerHTML = '<div class="alert alert-danger">Completa todos los campos.</div>'; return;
    }
    if (nueva !== confirmar) {
        alert.innerHTML = '<div class="alert alert-danger">Las contraseñas nuevas no coinciden.</div>'; return;
    }
    if (nueva.length < 6) {
        alert.innerHTML = '<div class="alert alert-danger">La contraseña debe tener al menos 6 caracteres.</div>'; return;
    }

    try {
        const data = await apiPost('../cambiar_contrasena.php', {
            id_usuario: <?= $id_usuario ?>,
            actual, nueva
        });
        if (data.status === 'success') {
            closeModal('modalContrasena');
            showToast('Contraseña actualizada correctamente ✓');
        } else {
            alert.innerHTML = `<div class="alert alert-danger">${data.message ?? 'Error al cambiar contraseña.'}</div>`;
        }
    } catch(e) {
        alert.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>';
    }
}
</script>
</body>
</html>
