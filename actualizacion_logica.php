<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $pass = $_POST['contrasena'];

    // Si la contraseña no está vacía, se actualiza también
    if (!empty($pass)) {
        $sql = "UPDATE usuario SET nombre='$nombre', correo='$correo', contrasena='$pass' WHERE id_usuario = $id";
    } else {
        // Si está vacía, solo actualizamos nombre y correo
        $sql = "UPDATE usuario SET nombre='$nombre', correo='$correo' WHERE id_usuario = $id";
    }

    if (mysqli_query($conexion, $sql)) {
        header("Location: panel_admin.php?section=usuarios&status=updated");
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>