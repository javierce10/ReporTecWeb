<?php
require_once '../auth_check.php';
if ($tipo !== 'admin') { header('Location: ../index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Aulas – ReporTec</title>
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
            <div class="sidebar-user-info"><strong><?= htmlspecialchars($nombre) ?></strong><small>Administrador</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Gestión</div>
            <a class="nav-item" href="admin.php"><i class="fas fa-home"></i> Panel principal</a>
            <a class="nav-item" href="gestionar_usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a class="nav-item" href="gestionar_departamentos.php"><i class="fas fa-building"></i> Departamentos</a>
            <a class="nav-item active" href="gestionar_aulas.php"><i class="fas fa-door-open"></i> Aulas</a>
            <a class="nav-item" href="gestionar_tipos.php"><i class="fas fa-tags"></i> Tipos de incidencia</a>
            <a class="nav-item" href="analiticas.php"><i class="fas fa-chart-bar"></i> Analíticas</a>
            <a class="nav-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi perfil</a>
        </nav>
        <div class="sidebar-footer">
            <button class="btn-logout" onclick="location.href='../logout.php'"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <span class="topbar-title">Gestionar Aulas</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modalAgregar')"><i class="fas fa-plus"></i> Nueva aula</button>
        </div>

        <div class="page-body anim">
            <div class="table-card">
                <div class="table-toolbar">
                    <div class="search-box"><i class="fas fa-search"></i>
                        <input type="text" id="buscador" placeholder="Buscar aula..." oninput="filtrar()">
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="cargarDatos()"><i class="fas fa-sync-alt"></i></button>
                </div>
                <div style="overflow-x:auto">
                    <table>
                        <thead><tr><th>#</th><th>Nombre</th><th>Edificio</th><th>Acciones</th></tr></thead>
                        <tbody id="tbodyAulas">
                            <tr><td colspan="4" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal agregar -->
<div class="modal-overlay" id="modalAgregar">
    <div class="modal" style="max-width:420px">
        <div class="modal-header"><h3>Nueva aula</h3>
            <button class="modal-close" onclick="closeModal('modalAgregar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaAgregar"></div>
            <div class="form-row">
                <div class="field"><label>Nombre del aula</label><input type="text" id="nNombre" placeholder="Ej: A-101"></div>
                <div class="field"><label>Edificio</label><input type="text" id="nEdificio" placeholder="Ej: Edificio A"></div>
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
    <div class="modal" style="max-width:420px">
        <div class="modal-header"><h3>Editar aula</h3>
            <button class="modal-close" onclick="closeModal('modalEditar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaEditar"></div>
            <input type="hidden" id="eId">
            <div class="form-row">
                <div class="field"><label>Nombre</label><input type="text" id="eNombre"></div>
                <div class="field"><label>Edificio</label><input type="text" id="eEdificio"></div>
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
let datos = [];

async function cargarDatos() {
    const tbody = document.getElementById('tbodyAulas');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>';
    try {
        datos = await apiGet('../get_aulas.php');
        renderTabla(datos);
    } catch(e) { tbody.innerHTML = '<tr><td colspan="4"><div class="alert alert-danger">Error al cargar.</div></td></tr>'; }
}

function filtrar() {
    const q = document.getElementById('buscador').value.toLowerCase();
    renderTabla(datos.filter(a =>
        (a.nombre ?? '').toLowerCase().includes(q) ||
        (a.edificio ?? '').toLowerCase().includes(q)
    ));
}

function renderTabla(lista) {
    const tbody = document.getElementById('tbodyAulas');
    if (!lista.length) { tbody.innerHTML = '<tr><td colspan="4"><div class="empty-state"><i class="fas fa-door-open"></i><p>Sin aulas.</p></div></td></tr>'; return; }
    tbody.innerHTML = lista.map(a => `<tr>
        <td style="color:var(--gray)">${a.id_aula}</td>
        <td><strong>${a.nombre}</strong></td>
        <td><span class="badge badge-admin">${a.edificio}</span></td>
        <td><div style="display:flex;gap:6px">
            <button class="btn btn-outline btn-icon btn-sm" onclick="abrirEditar(${a.id_aula},'${a.nombre.replace(/'/g,"\\'")}','${(a.edificio??'').replace(/'/g,"\\'")}')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger btn-sm" onclick="eliminar(${a.id_aula},'${a.nombre.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
        </div></td>
    </tr>`).join('');
}

function abrirEditar(id, nombre, edificio) {
    document.getElementById('eId').value = id;
    document.getElementById('eNombre').value = nombre;
    document.getElementById('eEdificio').value = edificio;
    document.getElementById('alertaEditar').innerHTML = '';
    openModal('modalEditar');
}

async function guardarEdicion() {
    const id = document.getElementById('eId').value;
    const nombre = document.getElementById('eNombre').value.trim();
    const edificio = document.getElementById('eEdificio').value.trim();
    const alerta = document.getElementById('alertaEditar');
    if (!nombre || !edificio) { alerta.innerHTML = '<div class="alert alert-danger">Completa los campos.</div>'; return; }
    const data = await apiPost('../update_aula.php', { id_aula: id, nombre, edificio });
    if (data.status === 'success') { closeModal('modalEditar'); showToast('Aula actualizada'); cargarDatos(); }
    else { alerta.innerHTML = `<div class="alert alert-danger">${data.message}</div>`; }
}

async function agregar() {
    const nombre = document.getElementById('nNombre').value.trim();
    const edificio = document.getElementById('nEdificio').value.trim();
    const alerta = document.getElementById('alertaAgregar');
    if (!nombre || !edificio) { alerta.innerHTML = '<div class="alert alert-danger">Completa los campos.</div>'; return; }
    const data = await apiPost('../add_aula.php', { nombre, edificio });
    if (data.status === 'success') { closeModal('modalAgregar'); showToast('Aula creada'); cargarDatos(); }
    else { alerta.innerHTML = `<div class="alert alert-danger">${data.message ?? 'Error.'}</div>`; }
}

async function eliminar(id, nombre) {
    if (!confirm(`¿Eliminar el aula "${nombre}"?`)) return;
    const data = await apiPost('../delete_aula.php', { id_aula: id });
    if (data.status === 'success') { showToast('Aula eliminada'); cargarDatos(); }
    else showToast(data.message ?? 'Error al eliminar', 'error');
}

cargarDatos();
</script>
</body>
</html>
