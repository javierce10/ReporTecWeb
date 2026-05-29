<?php
require_once '../auth_check.php';
$home = ($tipo === 'admin') ? 'admin.php' : (($tipo === 'jefe') ? 'jefe.php' : 'profesor.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Reporte – ReporTec</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-solo">
                <img src="../imagenes/logoiti.PNG" alt="Logo TecNM">
            </div>
            <div class="sidebar-sub" style="padding-left: 0; text-align: center;">TecNM · Campus Iguala</div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inicial ?></div>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <small><?= ucfirst($tipo) ?></small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Acciones</div>
            <a class="nav-item" href="<?= $home ?>"><i class="fas fa-home"></i> Inicio</a>
            <a class="nav-item active" href="crear_reporte.php"><i class="fas fa-plus-circle"></i> Crear reporte</a>
            <a class="nav-item" href="mis_reportes.php"><i class="fas fa-clipboard-list"></i> Mis reportes</a>
            <?php if ($tipo === 'jefe'): ?>
            <a class="nav-item" href="reportes_recibidos.php"><i class="fas fa-inbox"></i> Reportes recibidos</a>
            <?php endif; ?>
            <div class="nav-section">Cuenta</div>
            <a class="nav-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi perfil</a>
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
            <span class="topbar-title">Crear Reporte</span>
            <a href="<?= $home ?>" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div class="page-body anim">
            <div class="section-header">
                <div>
                    <div class="section-title">Nueva incidencia</div>
                    <div class="section-sub">Completa los datos para registrar el reporte</div>
                </div>
            </div>

            <div id="alertaDiv"></div>

            <div class="form-card">
                <div class="form-row cols-2">
                    <div class="field">
                        <label><i class="fas fa-door-open"></i> Aula</label>
                        <select id="selAula">
                            <option value="">Cargando aulas...</option>
                        </select>
                    </div>
                    <div class="field">
                        <label><i class="fas fa-tag"></i> Tipo de incidencia</label>
                        <select id="selTipo">
                            <option value="">Cargando tipos...</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label><i class="fas fa-align-left"></i> Descripción</label>
                        <textarea id="txtDesc" placeholder="Describe detalladamente la incidencia..."></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label><i class="fas fa-camera"></i> Fotografía de evidencia (opcional)</label>
                        <div class="foto-preview" id="fotoPreview" onclick="document.getElementById('inputFoto').click()">
                            <div class="foto-placeholder">
                                <i class="fas fa-camera"></i>
                                Haz clic para tomar o seleccionar una foto
                            </div>
                        </div>
                        <input type="file" id="inputFoto" accept="image/*" capture="environment" style="display:none" onchange="previsualizarFoto(this)">
                        <button type="button" class="btn btn-outline btn-sm" style="margin-top:8px;display:none" id="btnRemoverFoto" onclick="removerFoto()">
                            <i class="fas fa-trash"></i> Remover foto
                        </button>
                    </div>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px">
                    <a href="<?= $home ?>" class="btn btn-outline">Cancelar</a>
                    <button class="btn btn-primary" id="btnEnviar" onclick="enviarReporte()">
                        <i class="fas fa-paper-plane"></i> Enviar reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
<script>
const ID_USUARIO = <?= $id_usuario ?>;

// Cargar aulas y tipos al abrir
async function cargarSelects() {
    try {
        const [aulas, tipos] = await Promise.all([
            apiGet('../get_aulas.php'),
            apiGet('../get_tipos.php'),
        ]);

        const selA = document.getElementById('selAula');
        selA.innerHTML = '<option value="">-- Selecciona un aula --</option>' +
            aulas.map(a => `<option value="${a.id_aula}">${a.nombre} (${a.edificio})</option>`).join('');

        const selT = document.getElementById('selTipo');
        selT.innerHTML = '<option value="">-- Selecciona tipo --</option>' +
            tipos.map(t => `<option value="${t.id_tipo}">${t.nombre}</option>`).join('');
    } catch(e) {
        showToast('Error al cargar datos', 'error');
    }
}

function previsualizarFoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('fotoPreview').innerHTML = `<img src="${e.target.result}" style="max-height:200px;border-radius:10px">`;
        document.getElementById('btnRemoverFoto').style.display = 'inline-flex';
    };
    reader.readAsDataURL(file);
}

function removerFoto() {
    document.getElementById('inputFoto').value = '';
    document.getElementById('fotoPreview').innerHTML = `
        <div class="foto-placeholder">
            <i class="fas fa-camera"></i>
            Haz clic para tomar o seleccionar una foto
        </div>`;
    document.getElementById('btnRemoverFoto').style.display = 'none';
}

async function enviarReporte() {
    const desc  = document.getElementById('txtDesc').value.trim();
    const aula  = document.getElementById('selAula').value;
    const tipo  = document.getElementById('selTipo').value;
    const foto  = document.getElementById('inputFoto').files[0];
    const alerta = document.getElementById('alertaDiv');
    alerta.innerHTML = '';

    if (!desc) { alerta.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Escribe una descripción.</div>'; return; }
    if (!aula) { alerta.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Selecciona un aula.</div>'; return; }
    if (!tipo) { alerta.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Selecciona el tipo de incidencia.</div>'; return; }

    const btn = document.getElementById('btnEnviar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    try {
        const fd = new FormData();
        fd.append('id_usuario',   ID_USUARIO);
        fd.append('descripcion',  desc);
        fd.append('id_aula',      aula);
        fd.append('id_tipo',      tipo);
        if (foto) fd.append('evidencia', foto);

        const res  = await fetch('../crear_reporte.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.status === 'success') {
            showToast('Reporte enviado correctamente ✓');
            setTimeout(() => location.href = 'mis_reportes.php', 1500);
        } else {
            alerta.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message ?? 'Error al enviar.'}</div>`;
        }
    } catch(e) {
        alerta.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> No se pudo conectar al servidor.</div>';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar reporte';
    }
}

cargarSelects();
</script>
</body>
</html>
