<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Eliminar de las tablas hijas primero (por seguridad de integridad)
    mysqli_query($conexion, "DELETE FROM administrador WHERE id_usuario = $id");
    mysqli_query($conexion, "DELETE FROM jefe_departamento WHERE id_usuario = $id");
    mysqli_query($conexion, "DELETE FROM profesor WHERE id_usuario = $id");

    // 2. Eliminar de la tabla padre
    $sql_final = "DELETE FROM usuario WHERE id_usuario = $id";

    if (mysqli_query($conexion, $sql_final)) {
        header("Location: panel_admin.php?section=usuarios&msg=deleted");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>