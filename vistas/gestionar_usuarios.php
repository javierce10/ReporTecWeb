<?php
require_once '../auth_check.php';
if ($tipo !== 'admin') { header('Location: ../index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios – ReporTec</title>
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
                <small>Administrador</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Gestión</div>
            <a class="nav-item" href="admin.php"><i class="fas fa-home"></i> Panel principal</a>
            <a class="nav-item active" href="gestionar_usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a class="nav-item" href="gestionar_departamentos.php"><i class="fas fa-building"></i> Departamentos</a>
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
            <span class="topbar-title">Gestionar Usuarios</span>
            <button class="btn btn-primary btn-sm" onclick="openModal('modalAgregar')">
                <i class="fas fa-plus"></i> Nuevo usuario
            </button>
        </div>

        <div class="page-body anim">
            <div class="table-card">
                <div class="table-toolbar">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="buscador" placeholder="Buscar usuario..." oninput="filtrarTabla()">
                    </div>
                    <button class="btn btn-outline btn-sm" onclick="cargarUsuarios()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
                <div style="overflow-x:auto">
                    <table id="tablaUsuarios">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUsuarios">
                            <tr><td colspan="6" style="text-align:center;padding:40px">
                                <div class="spinner" style="margin:auto"></div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal agregar usuario -->
<div class="modal-overlay" id="modalAgregar">
    <div class="modal">
        <div class="modal-header">
            <h3>Nuevo usuario</h3>
            <button class="modal-close" onclick="closeModal('modalAgregar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaAgregar"></div>
            <div class="form-row cols-2">
                <div class="field">
                    <label>Nombre completo</label>
                    <input type="text" id="nuevoNombre" placeholder="Nombre completo">
                </div>
                <div class="field">
                    <label>Correo electrónico</label>
                    <input type="email" id="nuevoCorreo" placeholder="correo@ejemplo.com">
                </div>
            </div>
            <div class="form-row cols-2">
                <div class="field">
                    <label>Contraseña</label>
                    <input type="password" id="nuevaPass" placeholder="Contraseña">
                </div>
                <div class="field">
                    <label>Tipo de usuario</label>
                    <select id="nuevoTipo">
                        <option value="admin">Administrador</option>
                        <option value="jefe">Jefe de departamento</option>
                        <option value="profesor" selected>Profesor</option>
                    </select>
                </div>
            </div>
            <div class="form-row" id="campoDpto" style="display:none">
                <div class="field">
                    <label>Departamento (para jefe)</label>
                    <select id="nuevoDpto">
                        <option value="">-- Selecciona --</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalAgregar')">Cancelar</button>
            <button class="btn btn-primary" onclick="agregarUsuario()"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalEditar">
    <div class="modal">
        <div class="modal-header">
            <h3>Editar usuario</h3>
            <button class="modal-close" onclick="closeModal('modalEditar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div id="alertaEditar"></div>
            <input type="hidden" id="editId">
            <div class="form-row cols-2">
                <div class="field">
                    <label>Nombre completo</label>
                    <input type="text" id="editNombre">
                </div>
                <div class="field">
                    <label>Correo</label>
                    <input type="email" id="editCorreo">
                </div>
            </div>
            <div class="form-row">
                <div class="field">
                    <label>Nueva Contraseña (Dejar en blanco si no deseas cambiarla)</label>
                    <input type="password" id="editPass" placeholder="Escribe la nueva contraseña">
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
let usuariosData = [];
let departamentosData = [];

async function cargarUsuarios() {
    const tbody = document.getElementById('tbodyUsuarios');
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>';
    try {
        const [usuarios, dptos] = await Promise.all([
            apiGet('../get_usuarios.php'),
            apiGet('../get_departamentos.php'),
        ]);
        usuariosData = usuarios;
        departamentosData = dptos;

        // Llenar select de departamento en modal agregar
        const selDpto = document.getElementById('nuevoDpto');
        selDpto.innerHTML = '<option value="">-- Selecciona --</option>' +
            dptos.map(d => `<option value="${d.id_departamento}">${d.nombre}</option>`).join('');

        renderTabla(usuarios);
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6" class="alert alert-danger">Error al cargar datos.</td></tr>';
    }
}

function filtrarTabla() {
    const q = document.getElementById('buscador').value.toLowerCase();
    const filtrados = usuariosData.filter(u =>
        (u.nombre ?? '').toLowerCase().includes(q) ||
        (u.correo ?? '').toLowerCase().includes(q) ||
        (u.tipo ?? '').toLowerCase().includes(q)
    );
    renderTabla(filtrados);
}

function renderTabla(lista) {
    const tbody = document.getElementById('tbodyUsuarios');
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><i class="fas fa-users"></i><p>Sin usuarios encontrados.</p></div></td></tr>';
        return;
    }
    const badgeTipo = { admin: 'badge-admin', jefe: 'badge-jefe', profesor: 'badge-profesor' };
    tbody.innerHTML = lista.map(u => `
        <tr>
            <td style="color:var(--gray)">${u.id_usuario}</td>
            <td><strong>${u.nombre}</strong></td>
            <td>${u.correo}</td>
            <td><span class="badge ${badgeTipo[u.tipo] ?? ''}">${u.tipo}</span></td>
            <td>
                <span class="badge ${u.activo == 1 ? 'badge-resolved' : 'badge-rejected'}">
                    ${u.activo == 1 ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td>
                <div style="display:flex;gap:6px">
                    <button class="btn btn-outline btn-icon btn-sm" onclick="abrirEditar(${u.id_usuario},'${u.nombre.replace(/'/g,"\\'")}','${u.correo}')" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm ${u.activo == 1 ? 'btn-danger' : 'btn-teal'}"
                        onclick="toggleActivo(${u.id_usuario}, ${u.activo}, '${u.nombre.replace(/'/g,"\\'")}')">
                        ${u.activo == 1 ? '<i class="fas fa-ban"></i> Dar de baja' : '<i class="fas fa-check"></i> Activar'}
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function abrirEditar(id, nombre, correo) {
    document.getElementById('editId').value = id;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editCorreo').value = correo;
    document.getElementById('editPass').value = ''; // <-- Limpiamos el input de contraseña siempre
    document.getElementById('alertaEditar').innerHTML = '';
    openModal('modalEditar');
}

async function guardarEdicion() {
    const id         = document.getElementById('editId').value;
    const nombre     = document.getElementById('editNombre').value.trim();
    const correo     = document.getElementById('editCorreo').value.trim();
    const contrasena = document.getElementById('editPass').value; // <-- Capturamos el valor del nuevo input
    const alerta     = document.getElementById('alertaEditar');
    
    if (!nombre || !correo) { 
        alerta.innerHTML = '<div class="alert alert-danger">Completa los campos obligatorios.</div>'; 
        return; 
    }
    
    try {
        // Añadimos 'contrasena' al objeto de datos enviado al backend
        const data = await apiPost('../update_usuario.php', { 
            id_usuario: id, 
            nombre, 
            correo,
            contrasena: contrasena 
        });
        
        if (data.status === 'success') {
            closeModal('modalEditar'); 
            showToast('Usuario actualizado correctamente');
            cargarUsuarios();
        } else { 
            alerta.innerHTML = `<div class="alert alert-danger">${data.mensaje || 'Error al actualizar.'}</div>`; 
        }
    } catch(e) { 
        alerta.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>'; 
    }
}

async function toggleActivo(id, estadoActual, nombre) {
    // 1. Calculamos el nuevo estado que queremos mandar
    // Si el estado actual es 1 (activo), el nuevo estado será 0 (inactivo). Si es 0, será 1.
    const nuevoEstado = estadoActual == 1 ? 0 : 1;
    
    // Ajustamos el texto de confirmación de forma intuitiva
    const accion = nuevoEstado === 1 ? 'activar' : 'dar de baja';
    
    if (!confirm(`¿Deseas ${accion} al usuario "${nombre}"?`)) return;
    
    try {
        // 2. Agregamos el parámetro 'estado' en el objeto que se envía al servidor
        const data = await apiPost('../activar_usuario.php', { 
            id_usuario: id,
            estado: nuevoEstado // <-- AQUÍ ENVIAMOS EL NUEVO ESTADO DILIGENTEMENTE
        });
        
        if (data.status === 'success') { 
            showToast('Estado actualizado'); 
            cargarUsuarios(); 
        } else { 
            showToast(data.mensaje || 'Error al actualizar', 'error'); 
        }
    } catch(e) { 
        showToast('Error de conexión', 'error'); 
    }
}

async function agregarUsuario() {
    const nombre = document.getElementById('nuevoNombre').value.trim();
    const correo = document.getElementById('nuevoCorreo').value.trim();
    const pass   = document.getElementById('nuevaPass').value;
    const tipo   = document.getElementById('nuevoTipo').value;
    const dpto   = document.getElementById('nuevoDpto').value;
    const alerta = document.getElementById('alertaAgregar');
    alerta.innerHTML = '';

    if (!nombre || !correo || !pass) {
        alerta.innerHTML = '<div class="alert alert-danger">Completa los campos obligatorios.</div>'; return;
    }
    try {
        const data = await apiPost('../add_usuario.php', { nombre, correo, contrasena: pass, tipo, id_departamento: dpto });
        if (data.status === 'success') {
            closeModal('modalAgregar'); showToast('Usuario creado correctamente');
            cargarUsuarios();
        } else { alerta.innerHTML = `<div class="alert alert-danger">${data.message ?? 'Error al crear.'}</div>`; }
    } catch(e) { alerta.innerHTML = '<div class="alert alert-danger">Error de conexión.</div>'; }
}

// Mostrar/ocultar campo departamento según tipo
document.getElementById('nuevoTipo').addEventListener('change', function() {
    document.getElementById('campoDpto').style.display = this.value === 'jefe' ? 'grid' : 'none';
});

cargarUsuarios();
</script>
</body>
</html>
