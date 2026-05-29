<?php
require_once '../auth_check.php';
if ($tipo !== 'jefe' && $tipo !== 'admin') { header('Location: ../index.php'); exit; }
$home = ($tipo === 'admin') ? 'admin.php' : 'jefe.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Recibidos – ReporTec</title>
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
                <img src="../imagenes/Logoiti.png" alt="Logo TecNM">
            </div>
            <div class="sidebar-sub" style="padding-left: 0; text-align: center;">TecNM · Campus Iguala</div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inicial ?></div>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <small>Jefe de departamento</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Acciones</div>
            <a class="nav-item" href="<?= $home ?>"><i class="fas fa-home"></i> Inicio</a>
            <a class="nav-item" href="crear_reporte.php"><i class="fas fa-plus-circle"></i> Crear reporte</a>
            <a class="nav-item" href="mis_reportes.php"><i class="fas fa-clipboard-list"></i> Mis reportes</a>
            <a class="nav-item active" href="reportes_recibidos.php"><i class="fas fa-inbox"></i> Reportes recibidos</a>
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
            <span class="topbar-title">Reportes Recibidos</span>
            <button class="btn btn-outline btn-sm" onclick="cargarReportes()"><i class="fas fa-sync-alt"></i> Actualizar</button>
        </div>

        <div class="page-body anim">
            <div class="section-header">
                <div>
                    <div class="section-title">Reportes de mi departamento</div>
                    <div class="section-sub">Incidencias asignadas a tu departamento para gestionar</div>
                </div>
            </div>

            <!-- Filtros -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
                <button class="btn btn-outline btn-sm filtro-btn active" onclick="filtrar('todos', this)">Todos</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('Pendiente', this)" style="color:#E65100;border-color:#FFCC80">Pendiente</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('En proceso', this)" style="color:#1565C0;border-color:#90CAF9">En proceso</button>
                <button class="btn btn-outline btn-sm filtro-btn" onclick="filtrar('Resuelto', this)" style="color:#2E7D32;border-color:#A5D6A7">Resuelto</button>
            </div>

            <div id="listaReportes"><div class="loader"><div class="spinner"></div></div></div>
        </div>
    </div>
</div>

<!-- Modal cambiar estado -->
<div class="modal-overlay" id="modalEstado">
    <div class="modal" style="max-width:400px">
        <div class="modal-header">
            <h3>Actualizar estado</h3>
            <button class="modal-close" onclick="closeModal('modalEstado')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:#555;margin-bottom:16px">Selecciona el nuevo estado para el reporte <strong id="reporteIdModal"></strong>:</p>
            <div style="display:flex;flex-direction:column;gap:10px">
                <button class="btn btn-outline" style="justify-content:flex-start" onclick="cambiarEstado('Pendiente')">
                    <i class="fas fa-hourglass-half" style="color:#E65100"></i> Pendiente
                </button>
                <button class="btn btn-outline" style="justify-content:flex-start" onclick="cambiarEstado('En proceso')">
                    <i class="fas fa-sync-alt" style="color:#1565C0"></i> En proceso
                </button>
                <button class="btn btn-outline" style="justify-content:flex-start" onclick="cambiarEstado('Resuelto')">
                    <i class="fas fa-check-circle" style="color:#2E7D32"></i> Resuelto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal evidencia -->
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
let idReporteActivo = null;

async function cargarReportes() {
    const lista = document.getElementById('listaReportes');
    lista.innerHTML = '<div class="loader"><div class="spinner"></div></div>';
    try {
        const data = await apiGet(`../reportes_recibidos.php?id_usuario=${ID_USUARIO}`);
        todosLosReportes = Array.isArray(data) ? data : [];
        renderReportes();
    } catch(e) {
        lista.innerHTML = '<div class="alert alert-danger">Error al cargar reportes.</div>';
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
        lista.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No hay reportes recibidos.</p></div>';
        return;
    }

    lista.innerHTML = datos.map(r => {
        const estadoClass = r.estado?.toLowerCase().replace(' ', '-') || 'pendiente';
        return `
        <div class="reporte-card ${estadoClass}">
            <div class="reporte-top">
                <div>
                    <div style="font-weight:700;font-size:15px">${r.tipo_incidencia ?? 'Sin tipo'}</div>
                    <div class="reporte-id">#${r.id_incidencia} · ${r.nombre_aula ?? ''} · <b>${r.nombre_usuario ?? r.reportado_por ?? ''}</b></div>
                </div>
                <div style="display:flex;align-items:flex-start;gap:8px">
                    ${badgeEstado(r.estado)}
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <button class="btn btn-primary btn-sm" onclick="abrirModalEstado(${r.id_incidencia})">
                            <i class="fas fa-edit"></i> Estado
                        </button>
                        ${r.foto_evidencia ? `<button class="btn btn-outline btn-sm" onclick="verEvidencia('${r.foto_evidencia}')">
                            <i class="fas fa-image"></i> Evidencia
                        </button>` : ''}
                    </div>
                </div>
            </div>
            <div class="reporte-desc">${r.descripcion ?? ''}</div>
            <div class="reporte-meta">
                <span><i class="fas fa-calendar"></i> ${fechaLegible(r.datetime)}</span>
                <span><i class="fas fa-building"></i> ${r.departamento ?? ''}</span>
            </div>
        </div>`;
    }).join('');
}

function abrirModalEstado(idIncidencia) {
    idReporteActivo = idIncidencia;
    document.getElementById('reporteIdModal').textContent = '#' + idIncidencia;
    openModal('modalEstado');
}

async function cambiarEstado(nuevoEstado) {
    if (!idReporteActivo) return;
    try {
        const data = await apiPost('../actualizar_estatus.php', {
            id_incidencia:  idReporteActivo,
            id_responsable: ID_USUARIO,
            estado:         nuevoEstado,
        });
        if (data.status === 'success') {
            closeModal('modalEstado');
            showToast(`Estado actualizado a "${nuevoEstado}"`);
            // Actualizar localmente
            const r = todosLosReportes.find(x => x.id_incidencia == idReporteActivo);
            if (r) r.estado = nuevoEstado;
            renderReportes();
        } else {
            showToast('Error al actualizar estado', 'error');
        }
    } catch(e) {
        showToast('Error de conexión', 'error');
    }
}

function verEvidencia(imagen) {
    let src = imagen || '';
    if (src && !src.startsWith('http') && !src.startsWith('/')) {
        src = src.startsWith('../uploads/') ? `../${src}` : `../uploads/${src}`;
    }
    document.getElementById('imgEvidencia').src = src;
    openModal('modalEvidencia');
}

cargarReportes();
</script>
</body>
</html>
