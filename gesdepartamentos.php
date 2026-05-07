<?php
if (!isset($conexion)) {
    include "db.php"; 
}

// 1. Consulta para la tabla de departamentos
$query = "SELECT d.id_departamento, d.nombre AS nombre_dep, u.nombre AS nombre_jefe, d.id_jefe 
          FROM departamento d
          LEFT JOIN usuario u ON d.id_jefe = u.id_usuario
          ORDER BY d.nombre ASC";
$result = mysqli_query($conexion, $query);

// 2. Consulta para obtener TODOS los jefes (Corregida según tu esquema)
// Filtramos a los usuarios que existen en la tabla 'jefe_departamento'
$query_todos_jefes = "SELECT u.id_usuario, u.nombre, 
                      (SELECT COUNT(*) FROM departamento WHERE id_jefe = u.id_usuario) as asignado
                      FROM usuario u 
                      INNER JOIN jefe_departamento jd ON u.id_usuario = jd.id_usuario"; 

$todos_jefes = mysqli_query($conexion, $query_todos_jefes);

// Verificación de seguridad para evitar el error de la línea 14/20
$jefes_data = [];
if($todos_jefes){
    while($j = mysqli_fetch_assoc($todos_jefes)) {
        $jefes_data[] = $j;
    }
} else {
    // Si la consulta falla, mostramos el error de MySQL para debuggear
    echo "Error en SQL: " . mysqli_error($conexion);
}
?>

<div class="gestion-container">
    <div class="header-section">
        <h2>Gestión de Departamentos</h2>
        <button class="btn-add" onclick="abrirModalDep()">
            <i class="fas fa-plus"></i> Nuevo Departamento
        </button>
    </div>

    <div class="filter-toolbar">
        <div class="search-container">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="busquedaNombre" onkeyup="buscarDep()" placeholder="Buscar departamento o jefe...">
            </div>
        </div>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Departamento</th>
                <th>Jefe Responsable</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr class="dep-row">
                    <td><span class="id-pill">#<?php echo $row['id_departamento']; ?></span></td>
                    <td><strong><?php echo $row['nombre_dep']; ?></strong></td>
                    <td>
                        <?php if($row['nombre_jefe']): ?>
                            <span class="badge jefe-de-departamento">
                                <i class="fas fa-user-tie"></i> <?php echo $row['nombre_jefe']; ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-style: italic;">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions-cell">
                        <button class="btn-action btn-edit-table" title="Editar" onclick='abrirEditarDep(<?php echo json_encode($row); ?>)'>
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete-table" title="Eliminar" onclick="eliminarDep(<?php echo $row['id_departamento']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL SIN CAMPO AREA -->
<div id="modalDep" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Nuevo Departamento</h3>
            <span style="cursor:pointer; font-size: 1.5rem;" onclick="cerrarModalDep()">&times;</span>
        </div>
        
        <div class="modal-body">
            <form id="formDep">
                <input type="hidden" name="id_departamento" id="dep_id">
                <input type="hidden" name="accion" id="dep_accion" value="crear">
                
                <div class="input-group">
                    <label>Nombre del Departamento</label>
                    <input type="text" name="nombre" id="dep_nombre" required placeholder="Ej. Ciencias de la Tierra">
                </div>

                <div class="input-group">
                    <label>Asignar Jefe de Departamento</label>
                    <select name="id_jefe" id="dep_id_jefe">
                        <!-- Se llena con renderizarSelectJefes() -->
                    </select>
                    <small id="helperJefe" style="color: #7f8c8d; font-size: 0.8rem; display: block; margin-top: 5px;"></small>
                </div>

                <button type="submit" class="btn-save">Guardar Departamento</button>
            </form>
        </div>
    </div>
</div>

<script>
// Lista de jefes inyectada desde PHP
const listaJefes = <?php echo json_encode($jefes_data); ?>;

function renderizarSelectJefes(idJefeActual = null) {
    const select = document.getElementById('dep_id_jefe');
    if(!select) return;

    select.innerHTML = '<option value="">-- Sin asignar (Vacante) --</option>';
    
    listaJefes.forEach(jefe => {
        // Regla de validación: Libre (asignado == 0) o es el jefe actual del departamento
        if (parseInt(jefe.asignado) === 0 || jefe.id_usuario == idJefeActual) {
            const opt = document.createElement('option');
            opt.value = jefe.id_usuario;
            opt.text = jefe.nombre;
            if (idJefeActual && jefe.id_usuario == idJefeActual) {
                opt.selected = true;
            }
            select.add(opt);
        }
    });

    document.getElementById('helperJefe').innerText = "* Solo jefes sin departamento asignado.";
}

function abrirModalDep() {
    document.getElementById('modalTitle').innerText = "Nuevo Departamento";
    document.getElementById('dep_accion').value = "crear";
    document.getElementById('formDep').reset();
    renderizarSelectJefes(null); 
    document.getElementById('modalDep').style.display = "block";
}

function cerrarModalDep() {
    document.getElementById('modalDep').style.display = "none";
}

function abrirEditarDep(datos) {
    document.getElementById('modalTitle').innerText = "Editar Departamento";
    document.getElementById('dep_accion').value = "editar";
    document.getElementById('dep_id').value = datos.id_departamento;
    document.getElementById('dep_nombre').value = datos.nombre_dep;
    
    renderizarSelectJefes(datos.id_jefe); 
    document.getElementById('modalDep').style.display = "block";
}

function buscarDep() {
    const filtro = document.getElementById("busquedaNombre").value.toLowerCase();
    const filas = document.querySelectorAll(".dep-row");
    filas.forEach(fila => {
        const texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
}

document.getElementById('formDep').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('procesar_departamento.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === "success") {
            location.reload();
        } else {
            alert("Error en el servidor: " + data);
        }
    })
    .catch(error => console.error('Error:', error));
};

function eliminarDep(id) {
    if(confirm("¿Estás seguro de eliminar este departamento?")) {
        const formData = new FormData();
        formData.append('accion', 'eliminar');
        formData.append('id_departamento', id);

        fetch('procesar_departamento.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === "success") location.reload();
        });
    }
}

window.onclick = function(event) {
    let modal = document.getElementById('modalDep');
    if (event.target == modal) cerrarModalDep();
}
</script>