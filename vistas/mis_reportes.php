<?php
require_once '../auth_check.php';
$home = ($tipo === 'admin') ? 'admin.php' : (($tipo === 'jefe') ? 'jefe.php' : 'profesor.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reportes – ReporTec</title>
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
                <img src="../imagenes/logoiti.png" alt="Logo TecNM">
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
            <a class="nav-item" href="crear_reporte.php"><i class="fas fa-plus-circle"></i> Crear reporte</a>
            <a class="nav-item active" href="mis_reportes.php"><i class="fas fa-clipboard-list"></i> Mis reportes</a>
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
            <span class="topbar-title">Mis Reportes</span>
            <div class="topbar-right">
                <button class="btn btn-outline btn-sm" onclick="cargarReportes()"><i class="fas fa-sync-alt"></i> Actualizar</button>
                <a href="crear_reporte.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo</a>
            </div>
        </div>

        <div class="page-body anim">
            <div class="section-header">
                <div>
                    <div class="section-title">Mis reportes enviados</div>
                    <div class="section-sub">Historial de incidencias que has reportado</div>
                </div>
            </div>

            <!-- Filtro de estado -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px" id="filtros">
                <button class="btn btn-outline btn-sm filtro-btn active" onclick="filtrar('todos', this)">Todos</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('Pendiente', this)" style="color:#E65100;border-color:#FFCC80">Pendiente</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('En proceso', this)" style="color:#1565C0;border-color:#90CAF9">En proceso</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('Resuelto', this)" style="color:#2E7D32;border-color:#A5D6A7">Resuelto</button>
            </div>

            <div id="listaReportes"><div class="loader"><div class="spinner"></div></div></div>
        </div>
    </div>
</div>

<!-- Modal ver evidencia -->
<div class="modal-overlay" id="modalEvidencia">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <h3>Evidencia fotográfica</h3>
            <button class="modal-close" onclick="closeModal('modalEvidencia')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="text-align:center">
            <img id="imgEvidencia" src="" style="max-width:100%;border-radius:12px">
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
<script>
const ID_USUARIO = <?= $id_usuario ?>;
let todosLosReportes = [];
let filtroActivo = 'todos';

async function cargarReportes() {
    const lista = document.getElementById('listaReportes');
    lista.innerHTML = '<div class="loader"><div class="spinner"></div></div>';
    try {
        const data = await apiGet(`../mis_reportes.php?id_usuario=${ID_USUARIO}`);
        todosLosReportes = Array.isArray(data) ? data : [];
        renderReportes();
    } catch(e) {
        lista.innerHTML = '<div class="alert alert-danger">No se pudo cargar los reportes.</div>';
    }
}

function filtrar(estado, btn) {
    filtroActivo = estado;
    document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderReportes();
}

function renderReportes() {
    const lista = document.getElementById('listaReportes');
    const datos = filtroActivo === 'todos'
        ? todosLosReportes
        : todosLosReportes.filter(r => r.estado === filtroActivo);

    if (!datos.length) {
        lista.innerHTML = `<div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No hay reportes${filtroActivo !== 'todos' ? ' con este estado' : ''}.</p>
            <a href="crear_reporte.php" class="btn btn-primary" style="margin-top:16px"><i class="fas fa-plus"></i> Crear reporte</a>
        </div>`;
        return;
    }

    lista.innerHTML = datos.map(r => {
        const estadoClass = r.estado?.toLowerCase().replace(' ', '-') || 'pendiente';
        return `
        <div class="reporte-card ${estadoClass}">
            <div class="reporte-top">
                <div>
                    <div style="font-weight:700;font-size:15px">${r.tipo_incidencia ?? 'Sin tipo'}</div>
                    <div class="reporte-id">#${r.id_incidencia} · ${r.nombre_aula ?? ''} · ${r.edificio ?? ''}</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    ${badgeEstado(r.estado)}
                    ${r.imagen ? `<button class="btn btn-outline btn-icon btn-sm" onclick="verEvidencia('${r.imagen}')" title="Ver foto">
                        <i class="fas fa-image"></i></button>` : ''}
                </div>
            </div>
            <div class="reporte-desc">${r.descripcion ?? ''}</div>
            <div class="reporte-meta">
                <span><i class="fas fa-calendar"></i> ${fechaLegible(r.datetime)}</span>
                ${r.estado_responsable ? `<span><i class="fas fa-user-check"></i> ${r.estado_responsable}</span>` : ''}
            </div>
        </div>`;
    }).join('');
}

function verEvidencia(imagen) {
    document.getElementById('imgEvidencia').src = `../uploads/${imagen}`;
    openModal('modalEvidencia');
}

// Estilo botón filtro activo
document.querySelectorAll('.filtro-btn').forEach(b => {
    b.addEventListener('click', () => {
        document.querySelectorAll('.filtro-btn').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
    });
});

cargarReportes();
</script>
</body>
</html>
