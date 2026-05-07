<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $pass = $_POST['contrasena']; // Recomiendo usar password_hash en el futuro
    $rol = $_POST['rol'];

    // 1. Insertar en la tabla base 'usuario'
    $sql_user = "INSERT INTO usuario (nombre, correo, contrasena) VALUES ('$nombre', '$correo', '$pass')";
    
    if (mysqli_query($conexion, $sql_user)) {
        $id_nuevo = mysqli_insert_id($conexion); // Obtenemos el ID generado

        // 2. Insertar en la tabla correspondiente según el rol
        if ($rol == 'admin') {
            $sql_rol = "INSERT INTO administrador (id_usuario) VALUES ($id_nuevo)";
        } elseif ($rol == 'jefe') {
            $sql_rol = "INSERT INTO jefe_departamento (id_usuario) VALUES ($id_nuevo)";
        } else {
            $sql_rol = "INSERT INTO profesor (id_usuario) VALUES ($id_nuevo)";
        }

        if (mysqli_query($conexion, $sql_rol)) {
            header("Location: panel_admin.php?section=usuarios&msg=success");
        }
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>