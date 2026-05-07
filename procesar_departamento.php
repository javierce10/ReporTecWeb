<?php
    include 'db.php';

    $accion = $_POST['accion'];

    if ($accion == 'crear' || $accion == 'editar') {
        $nombre = $_POST['nombre'];
        $id_jefe = !empty($_POST['id_jefe']) ? $_POST['id_jefe'] : "NULL";

        if ($accion == 'crear') {
            $sql = "INSERT INTO departamento (nombre, id_jefe) VALUES ('$nombre', $id_jefe)";
        } else {
            $id = $_POST['id_departamento'];
            $sql = "UPDATE departamento SET nombre='$nombre', id_jefe=$id_jefe WHERE id_departamento=$id";
        }

        if (mysqli_query($conexion, $sql)) {
            // Si hay un jefe, actualizamos su tabla según el diagrama
            if ($id_jefe != "NULL") {
                $dep_id = ($accion == 'crear') ? mysqli_insert_id($conexion) : $id;
                mysqli_query($conexion, "UPDATE jefe_departamento SET id_departamento = $dep_id WHERE id_usuario = $id_jefe");
            }
            echo "success";
        }
    }
?>