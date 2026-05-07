<?php
session_start();
include('db.php');

$correo = $_POST['correo'];
$password = $_POST['contrasena']; // Nota: En producción usa password_hash()

// 1. Verificar credenciales básicas
$query = "SELECT id_usuario, nombre FROM usuario WHERE correo = '$correo' AND contrasena = '$password'";
$resultado = mysqli_query($conexion, $query);

if (mysqli_num_rows($resultado) == 1) {
    $user = mysqli_fetch_assoc($resultado);
    $id = $user['id_usuario'];
    $_SESSION['usuario_nombre'] = $user['nombre'];
    $_SESSION['id_usuario'] = $id;

    // 2. Determinar Rol
    $admin = mysqli_query($conexion, "SELECT id_usuario FROM administrador WHERE id_usuario = $id");
    $jefe = mysqli_query($conexion, "SELECT id_usuario FROM jefe_departamento WHERE id_usuario = $id");
    $profesor = mysqli_query($conexion, "SELECT id_usuario FROM profesor WHERE id_usuario = $id");

    if (mysqli_num_rows($admin) > 0) {
        $_SESSION['rol'] = 'administrador';
    header("Location: panel_admin.php");
    } elseif (mysqli_num_rows($jefe) > 0) {
        $_SESSION['rol'] = 'jefe_departamento';
        header("Location: dashboard_jefe.php");
    } elseif (mysqli_num_rows($profesor) > 0) {
        $_SESSION['rol'] = 'profesor';
        header("Location: dashboard_profesor.php");
    }
} else {
    header("Location: index.php?error=1");
}
?>