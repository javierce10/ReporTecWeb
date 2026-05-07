<?php
// 1. Modificamos la consulta para que vengan ordenados por ROL
$query = "SELECT u.id_usuario, u.nombre, u.correo, 
          CASE 
            WHEN a.id_usuario IS NOT NULL THEN 'Administrador'
            WHEN j.id_usuario IS NOT NULL THEN 'Jefe de Departamento'
            WHEN p.id_usuario IS NOT NULL THEN 'Profesor'
            ELSE 'Sin Rol'
          END AS rol
          FROM usuario u
          LEFT JOIN administrador a ON u.id_usuario = a.id_usuario
          LEFT JOIN jefe_departamento j ON u.id_usuario = j.id_usuario
          LEFT JOIN profesor p ON u.id_usuario = p.id_usuario
          ORDER BY rol ASC, u.nombre ASC"; // Ordenado por rol
$usuarios = mysqli_query($conexion, $query);

$ultimo_rol = ""; // Variable para rastrear el cambio de grupo
?>

<div class="gestion-container">
    <div class="header-section">
        <h2>Gestión de Usuarios</h2>
        <button class="btn-add" onclick="toggleModal()">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>

    <div class="filter-toolbar">
        <div class="filter-group">
            <button class="btn-filter active" onclick="filtrarRol('todos')">Todos</button>
            <button class="btn-filter" onclick="filtrarRol('Administrador')">Administradores</button>
            <button class="btn-filter" onclick="filtrarRol('Jefe de Departamento')">Jefes</button>
            <button class="btn-filter" onclick="filtrarRol('Profesor')">Profesores</button>
        </div>
        
        <div class="search-container">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="busquedaNombre" onkeyup="buscarPorNombre()" placeholder="Buscar usuario por nombre o correo...">
            </div>
        </div>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($usuarios)): ?>
                
                <?php 
                // LÓGICA DE SEPARADORES
                if ($row['rol'] !== $ultimo_rol): 
                    $ultimo_rol = $row['rol'];
                ?>
                    <tr class="role-separator" data-rol-sep="<?php echo $row['rol']; ?>">
                        <td colspan="5">
                            <?php 
                                $rol = $row['rol'];
                                
                                // Dividimos el nombre del rol por espacios (ej. "Jefe", "de", "Departamento")
                                $palabras = explode(' ', $rol);
                                
                                // Pluralizamos solo la primera palabra
                                // Si termina en 'r' (Administrador, Profesor) -> 'es'. Si no (Jefe) -> 's'.
                                $plural = (substr($palabras[0], -1) == 'r') ? 'es' : 's';
                                $palabras[0] .= $plural;
                                
                                // Volvemos a unir las palabras
                                echo implode(' ', $palabras); 
                            ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr class="user-row" data-rol="<?php echo $row['rol']; ?>">
                    <td><strong><?php echo $row['nombre']; ?></strong></td>
                    <td><?php echo $row['correo']; ?></td>
                    <td>
                        <span class="badge <?php echo strtolower(str_replace(' ', '-', $row['rol'])); ?>">
                            <?php echo $row['rol']; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn-action btn-edit-table" title="Editar" 
                                onclick="abrirModalEditar('<?php echo $row['id_usuario']; ?>', '<?php echo $row['nombre']; ?>', '<?php echo $row['correo']; ?>')">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete-table" title="Eliminar" 
                                onclick="eliminarUsuario(<?php echo $row['id_usuario']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal para Nuevo Usuario Renovado -->
<div id="modalUsuario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Nuevo Usuario</h3>
            <span class="close" onclick="toggleModal()">&times;</span>
        </div>
        
        <div class="modal-body">
            <form action="procesar_usuario.php" method="POST">
                <div class="input-group">
                    <label><i class="fas fa-id-card"></i> Nombre Completo</label>
                    <input type="text" name="nombre" placeholder="Ej. Juan Pérez" required>
                </div>
                
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Correo Institucional</label>
                    <input type="email" name="correo" placeholder="correo@universidad.edu" required>
                </div>
                
                <div class="input-group">
                    <label><i class="fas fa-key"></i> Contraseña Temporal</label>
                    <input type="password" name="contrasena" placeholder="Mínimo 8 caracteres" required>
                </div>
                
                <div class="input-group">
                    <label><i class="fas fa-user-tag"></i> Rol del Usuario</label>
                    <select name="rol" required>
                        <option value="" disabled selected>Selecciona un cargo...</option>
                        <option value="profesor">Profesor</option>
                        <option value="jefe">Jefe de Departamento</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-save">Crear Cuenta</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal para MODIFICAR Usuario -->
<div id="modalEditarUsuario" class="modal">
    <div class="modal-content">
        <!-- Busca esta línea en el Modal de Edición -->
        <div class="modal-header" style="background: #003366;"> <!-- Cambiado a un gris oscuro elegante o usa var(--azul-principal) -->
            <h3><i class="fas fa-user-edit"></i> Actualizar Información</h3>
            <span class="close" onclick="cerrarModalEditar()">&times;</span>
        </div>
        
        <div class="modal-body">
            <form action="actualizacion_logica.php" method="POST">
                <!-- Campo oculto para el ID -->
                <input type="hidden" name="id_usuario" id="edit_id">
                
                <div class="input-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" id="edit_nombre" required>
                </div>
                
                <div class="input-group">
                    <label>Correo Institucional</label>
                    <input type="email" name="correo" id="edit_correo" required>
                </div>
                
                <div class="input-group">
                    <label>Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" name="contrasena" placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-save" style="background: #094887;">Actualizar Datos</button>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir el modal de edición y llenar los campos
function abrirModalEditar(id, nombre, correo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_correo').value = correo;
    document.getElementById('modalEditarUsuario').style.display = 'block';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarUsuario').style.display = 'none';
}

// Cerrar modales si se hace clic fuera de ellos
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<script>
function toggleModal() {
    const modal = document.getElementById('modalUsuario');
    modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
}

function eliminarUsuario(id) {
    if(confirm("¿Estás seguro de eliminar a este usuario? Esta acción no se puede deshacer.")) {
        window.location.href = "eliminar_usuario.php?id=" + id;
    }
}

</script>

<script>
    function filtrarRol(rol) {
        const filas = document.querySelectorAll('.user-row');
        const separadores = document.querySelectorAll('.role-separator');
        const botones = document.querySelectorAll('.btn-filter');

        // Cambiar estado activo
        botones.forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Filtrar filas de usuarios
        filas.forEach(fila => {
            const rolFila = fila.getAttribute('data-rol');
            fila.style.display = (rol === 'todos' || rolFila === rol) ? "" : "none";
        });

        // Ocultar separadores si no estamos en "todos"
        separadores.forEach(sep => {
            sep.style.display = (rol === 'todos') ? "" : "none";
        });
    }

    function buscarPorNombre() {
        const input = document.getElementById("busquedaNombre");
        const filtroTexto = input.value.toLowerCase();
        
        // 1. Identificar qué filtro de ROL está seleccionado
        const botonActivo = document.querySelector('.btn-filter.active');
        
        // 2. Obtener el "texto" del filtro activo para saber qué estamos viendo
        // Usamos el texto del botón: "Todos", "Administradores", "Jefes" o "Profesores"
        const categoriaActiva = botonActivo ? botonActivo.innerText.trim() : "Todos";
        
        const filas = document.querySelectorAll(".user-row");
        const separadores = document.querySelectorAll(".role-separator");

        filas.forEach(fila => {
            const nombre = fila.cells[1].textContent.toLowerCase();
            const correo = fila.cells[2].textContent.toLowerCase();
            const rolFila = fila.getAttribute('data-rol'); // "Administrador", "Profesor", etc.

            // LÓGICA DE COINCIDENCIA DE TEXTO
            const coincideTexto = nombre.includes(filtroTexto) || correo.includes(filtroTexto);
            
            // LÓGICA DE COINCIDENCIA DE ROL (Mapeo manual para evitar errores de plural)
            let coincideRol = false;
            if (categoriaActiva === "Todos") {
                coincideRol = true;
            } else if (categoriaActiva === "Administradores" && rolFila === "Administrador") {
                coincideRol = true;
            } else if (categoriaActiva === "Jefes" && rolFila === "Jefe de Departamento") {
                coincideRol = true;
            } else if (categoriaActiva === "Profesores" && rolFila === "Profesor") {
                coincideRol = true;
            }

            // Mostrar solo si cumple ambas
            fila.style.display = (coincideTexto && coincideRol) ? "" : "none";
        });

        // 3. Manejo de separadores (encabezados de grupo)
        separadores.forEach(sep => {
            const rolSep = sep.getAttribute('data-rol-sep');
            
            // El separador solo se muestra si:
            // A) Estamos en la pestaña "Todos"
            // B) Hay al menos un usuario visible bajo ese separador
            const tieneHijosVisibles = Array.from(filas).some(f => 
                f.getAttribute('data-rol') === rolSep && f.style.display === ""
            );

            if (categoriaActiva === "Todos") {
                sep.style.display = tieneHijosVisibles ? "" : "none";
            } else {
                // Si estamos en una pestaña específica (ej. Profesores), 
                // no queremos ver el separador gris, ya que es redundante.
                sep.style.display = "none";
            }
        });
    }
</script>

