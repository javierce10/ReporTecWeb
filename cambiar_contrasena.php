<?php
include 'conexion.php';

$id_usuario       = $_POST['id_usuario'];
$contrasena_actual = $_POST['contrasena_actual'];
$contrasena_nueva  = $_POST['contrasena_nueva'];

// Verificar que la contraseña actual sea correcta
$sql = "SELECT id_usuario FROM usuario WHERE id_usuario = '$id_usuario' AND contrasena = '$contrasena_actual'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["status" => "wrong_password"]);
    $conn->close();
    exit;
}

// Actualizar la contraseña
$sql = "UPDATE usuario SET contrasena = '$contrasena_nueva' WHERE id_usuario = '$id_usuario'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}

$conn->close();
?>