<?php
// Incluimos la conexión a la base de datos
include "db.php";

// Verificamos que se haya recibido una acción
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // 1. ACCIÓN: CREAR AULA
    if ($accion == "crear") {
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $edificio = mysqli_real_escape_string($conexion, $_POST['edificio']);

        $query = "INSERT INTO aula (nombre, edificio) VALUES ('$nombre', '$edificio')";
        
        if (mysqli_query($conexion, $query)) {
            echo "success";
        } else {
            echo "Error al crear: " . mysqli_error($conexion);
        }
    }

    // 2. ACCIÓN: EDITAR AULA
    if ($accion == "editar") {
        $id_aula = mysqli_real_escape_string($conexion, $_POST['id_aula']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $edificio = mysqli_real_escape_string($conexion, $_POST['edificio']);

        $query = "UPDATE aula SET nombre = '$nombre', edificio = '$edificio' WHERE id_aula = '$id_aula'";
        
        if (mysqli_query($conexion, $query)) {
            echo "success";
        } else {
            echo "Error al actualizar: " . mysqli_error($conexion);
        }
    }

    // 3. ACCIÓN: ELIMINAR AULA
    if ($accion == "eliminar") {
        $id_aula = mysqli_real_escape_string($conexion, $_POST['id_aula']);

        // Nota: Si hay incidencias amarradas a esta aula, la eliminación podría fallar 
        // por integridad referencial (llaves foráneas).
        $query = "DELETE FROM aula WHERE id_aula = '$id_aula'";
        
        if (mysqli_query($conexion, $query)) {
            echo "success";
        } else {
            echo "Error al eliminar: " . mysqli_error($conexion);
        }
    }
} else {
    echo "No se recibió ninguna acción válida.";
}

mysqli_close($conexion);
?>