<?php
require_once '../auth_check.php';
if ($tipo !== 'admin') { header('Location: ../index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Departamentos – ReporTec</title>
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
                <small>Administrador</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Gestión</div>
            <a class="nav-item" href="admin.php"><i class="fas fa-home"></i> Panel principal</a>
            <a class="nav-item" href="gestionar_usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a class="nav-item active" href="gestionar_departamentos.php"><i class="fas fa-building"></i> Departamentos</a>
            <a class="nav-item" href="gestionar_aulas.php"><i class="fas fa-door-open"></i> Aulas</a>
            <a class="nav-item" href="gestionar_tipos.php"><i class="fas fa-tags"></i> Tipos de incidencia</a>
            <a class="nav-item" href="analiticas.php"><i class="fas fa-chart-bar"></i> Analíticas</a>
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
            <span class="topbar-title">Gestionar Departamentos</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modalAgregar')">
                <i class="fas fa-plus"></i> Nuevo departamento
            </button>
        </div>

        <div class="page-body anim">
            <div class="table-card">
                <div class="table-toolbar">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="buscador" placeholder="Buscar..." oninput="filtrar()">
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="cargarDatos()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div style="overflow-x:auto">
                    <table id="tablaDptos">
                        <thead>
                            <tr><th>#</th><th>Nombre</th><th>Área</th><th>Jefe asignado</th><th>Acciones</th></tr>
                        </thead>
                        <tbody id="tbodyDptos">
                            <tr><td colspan="5" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal agregar -->
<div class="modal-overlay" id="modalAgregar">
    <div class="modal">
        <div class="modal-header">
            <h3>Nuevo departamento</h3>
            <button class="modal-close" onclick="closeModal('modalAgregar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaAgregar"></div>
            <div class="form-row">
                <div class="field"><label>Nombre del departamento</label><input type="text" id="nNombre" placeholder="Ej: Sistemas Computacionales"></div>
                <div class="field"><label>Área</label><input type="text" id="nArea" placeholder="Ej: Ingeniería"></div>
                <div class="field"><label>Jefe asignado (opcional)</label>
                    <select id="nJefe"><option value="">-- Sin jefe asignado --</option></select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalAgregar')">Cancelar</button>
            <button class="btn btn-primary" onclick="agregar()"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </div>
</div>

<!-- Modal editar -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal">
        <div class="modal-header">
            <h3>Editar departamento</h3>
            <button class="modal-close" onclick="closeModal('modalEditar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaEditar"></div>
            <input type="hidden" id="eId">
            <div class="form-row">
                <div class="field"><label>Nombre</label><input type="text" id="eNombre"></div>
                <div class="field"><label>Área</label><input type="text" id="eArea"></div>
                <div class="field"><label>Jefe</label>
                    <select id="eJefe"><option value="">-- Sin jefe --</option></select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalEditar')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarEdicion()"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
<script>
let datos = [], jefes = [];

async function cargarDatos() {
    const tbody = document.getElementById('tbodyDptos');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>';
    try {
        [datos, jefes] = await Promise.all([
            apiGet('../get_departamentos.php'),
            apiGet('../get_jefes_disponibles.php'),
        ]);
        // Llenar selects de jefe
        const opts = '<option value="">-- Sin jefe --</option>' +
            jefes.map(j => `<option value="${j.id_usuario}">${j.nombre}</option>`).join('');
        document.getElementById('nJefe').innerHTML = opts.replace('-- Sin jefe --','-- Sin jefe asignado --');
        document.getElementById('eJefe').innerHTML = opts;
        renderTabla(datos);
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="alert alert-danger">Error al cargar.</div></td></tr>';
    }
}

function filtrar() {
    const q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(datos.filter(d =>
        (d.nombre ?? '').toLowerCase().includes(q) ||
        (d.area ?? '').toLowerCase().includes(q) ||
        (d.nombre_jefe ?? '').toLowerCase().includes(q)
    ));
}

function renderTabla(lista) {
    const tbody = document.getElementById('tbodyDptos');
    if (!lista.length) { tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><i class="fas fa-building"></i><p>Sin departamentos.</p></div></td></tr>'; return; }
    tbody.innerHTML = lista.map(d => `<tr>
        <td style="color:var(--gray)">${d.id_departamento}</td>
        <td><strong>${d.nombre}</strong></td>
        <td>${d.area ?? '-'}</td>
        <td>${d.nombre_jefe ?? '<span style="color:var(--gray)">Sin jefe</span>'}</td>
        <td><div style="display:flex;gap:6px">
            <button class="btn btn-outline btn-icon btn-sm" onclick="abrirEditar(${d.id_departamento},'${d.nombre.replace(/'/g,"\\'")}','${(d.area??'').replace(/'/g,"\\'")}','${d.id_jefe??''}')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger btn-sm" onclick="eliminar(${d.id_departamento},'${d.nombre.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
        </div></td>
    </tr>`).join('');
}

function abrirEditar(id, nombre, area, idJefe) {
    document.getElementById('eId').value = id;
    document.getElementById('eNombre').value = nombre;
    document.getElementById('eArea').value = area;
    document.getElementById('eJefe').value = idJefe || '';
    document.getElementById('alertaEditar').innerHTML = '';
    openModal('modalEditar');
}

async function guardarEdicion() {
    const id = document.getElementById('eId').value;
    const nombre = document.getElementById('eNombre').value.trim();
    const area = document.getElementById('eArea').value.trim();
    const jefe = document.getElementById('eJefe').value;
    const alerta = document.getElementById('alertaEditar');
    if (!nombre) { alerta.innerHTML = '<div class="alert alert-danger">El nombre es obligatorio.</div>'; return; }
    try {
        const data = await apiPost('../update_departamento.php', { id_departamento: id, nombre, area, id_jefe: jefe });
        if (data.status === 'success') { closeModal('modalEditar'); showToast('Departamento actualizado'); cargarDatos(); }
        else { alerta.innerHTML = `<div class="alert alert-danger">${data.message}</div>`; }
    } catch(e) { alerta.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>'; }
}

async function agregar() {
    const nombre = document.getElementById('nNombre').value.trim();
    const area   = document.getElementById('nArea').value.trim();
    const jefe   = document.getElementById('nJefe').value;
    const alerta = document.getElementById('alertaAgregar');
    if (!nombre) { alerta.innerHTML = '<div class="alert alert-danger">El nombre es obligatorio.</div>'; return; }
    try {
        const data = await apiPost('../add_departamento.php', { nombre, area, id_jefe: jefe });
        if (data.status === 'success') { closeModal('modalAgregar'); showToast('Departamento creado'); cargarDatos(); }
        else { alerta.innerHTML = `<div class="alert alert-danger">${data.message ?? 'Error.'}</div>`; }
    } catch(e) { alerta.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>'; }
}

async function eliminar(id, nombre) {
    if (!confirm(`¿Eliminar el departamento "${nombre}"?`)) return;
    try {
        const data = await apiPost('../delete_departamento.php', { id_departamento: id });
        if (data.status === 'success') { showToast('Departamento eliminado'); cargarDatos(); }
        else showToast(data.message ?? 'Error al eliminar', 'error');
    } catch(e) { showToast('Error de conexión', 'error'); }
}

cargarDatos();
</script>
</body>
</html>
