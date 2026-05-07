<?php
// Usamos la conexión $conexion que ya viene de panel_admin.php

// 1. Consulta para obtener las aulas
$query = "SELECT id_aula, nombre, edificio FROM aula ORDER BY edificio ASC, nombre ASC";
$result = mysqli_query($conexion, $query);

// 2. Manejo de errores de consulta
if (!$result) {
    echo "<div class='alert alert-danger'>Error al cargar aulas: " . mysqli_error($conexion) . "</div>";
}

$edificio_actual = "";

?>

<div class="gestion-container">
    <div class="header-section">
        <h2>Gestión de Aulas y Edificios</h2>
        <button class="btn-add" onclick="abrirModalAula()">
            <i class="fas fa-plus"></i> Nueva Aula
        </button>
    </div>

<div class="filter-toolbar">
    <div class="search-container">
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <!-- Usamos el ID busquedaNombre para heredar los estilos de tu CSS global -->
            <input type="text" id="busquedaNombre" onkeyup="buscarAula()" placeholder="Buscar por aula, laboratorio o edificio...">
        </div>
    </div>
</div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Nombre del Aula</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    // LÓGICA DE SEPARACIÓN:
                    // Si el edificio de esta fila es diferente al anterior, imprimimos un encabezado
                    if ($row['edificio'] !== $edificio_actual): 
                        $edificio_actual = $row['edificio'];
                ?>
                    <tr class="edificio-separator">
                        <td colspan="3" style="background-color: #f8fafc; padding: 12px 20px; border-left: 4px solid var(--primary-color);">
                            <h3 style="margin: 0; color: var(--primary-color); font-size: 1.1rem;">
                                <i class="fas fa-building"></i> Edificio <?php echo $edificio_actual; ?>
                            </h3>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr class="aula-row">
                    <td>
                        <strong><?php echo $row['nombre']; ?></strong>
                        <br>
                        <small style="color: #64748b;">Ubicación: <?php echo $row['edificio']; ?></small>
                    </td>
                    <td class="actions-cell">
                        <button class="btn-action btn-edit-table" title="Editar" 
                                onclick='abrirEditarAula(<?php echo json_encode($row); ?>)'>
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete-table" title="Eliminar" 
                                onclick="eliminarAula(<?php echo $row['id_aula']; ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No hay aulas registradas.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- MODAL PARA GESTIÓN DE AULAS -->
<div id="modalAula" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitleAula">Nueva Aula</h3>
            <span style="cursor:pointer; font-size: 1.5rem;" onclick="cerrarModalAula()">&times;</span>
        </div>
        
        <div class="modal-body">
            <form id="formAula">
                <input type="hidden" name="id_aula" id="aula_id">
                <input type="hidden" name="accion" id="aula_accion" value="crear">
                
                <div class="input-group">
                    <label>Nombre del Aula / Laboratorio</label>
                    <input type="text" name="nombre" id="aula_nombre" required placeholder="Ej. Laboratorio de Redes">
                </div>

                <div class="input-group">
                    <label>Edificio / Ubicación</label>
                    <input type="text" name="edificio" id="aula_edificio" required placeholder="Ej. Edificio K">
                </div>

                <button type="submit" class="btn-save" id="btnGuardarAula">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>

<script>
// Abrir modal para crear
function abrirModalAula() {
    document.getElementById('modalTitleAula').innerText = "Nueva Aula";
    document.getElementById('aula_accion').value = "crear";
    document.getElementById('formAula').reset();
    document.getElementById('modalAula').style.display = "block";
}

// Abrir modal para editar
function abrirEditarAula(datos) {
    document.getElementById('modalTitleAula').innerText = "Editar Aula";
    document.getElementById('aula_accion').value = "editar";
    document.getElementById('aula_id').value = datos.id_aula;
    document.getElementById('aula_nombre').value = datos.nombre;
    document.getElementById('aula_edificio').value = datos.edificio;
    document.getElementById('modalAula').style.display = "block";
}

function cerrarModalAula() {
    document.getElementById('modalAula').style.display = "none";
}

// Buscador en tiempo real
function buscarAula() {
    // Referencia al nuevo ID para que el estilo y la lógica coincidan
    const filtro = document.getElementById("busquedaNombre").value.toLowerCase();
    const filas = document.querySelectorAll(".aula-row");
    const separadores = document.querySelectorAll(".edificio-separator");
    
    filas.forEach(fila => {
        const texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });

    // Lógica extra: Ocultar los títulos de edificios si no hay aulas visibles en ellos
    // Solo si quieres que los separadores también se filtren
    if(filtro === "") {
        separadores.forEach(s => s.style.display = "");
    } else {
        separadores.forEach(s => s.style.display = "none");
    }}

// Envío por AJAX a procesar_aula.php
document.getElementById('formAula').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('./procesar_aulas.php', { // <-- Actualizado a procesar_aulas.php
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === "success") {
            location.reload();
        } else {
            alert("Error: " + data);
        }
    });
};

function eliminarAula(id) {
    if(confirm("¿Estás seguro de eliminar esta aula?")) {
        const formData = new FormData();
        formData.append('accion', 'eliminar');
        formData.append('id_aula', id);

        fetch('./procesar_aulas.php', { // <-- Actualizado a procesar_aulas.php
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === "success") location.reload();
        });
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    let modal = document.getElementById('modalAula');
    if (event.target == modal) cerrarModalAula();
}
</script>