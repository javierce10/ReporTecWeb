<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$tipo = $_POST['tipo'];

// 🔥 1. Insertar en usuario
$sql = "INSERT INTO usuario (nombre, correo, contrasena)
        VALUES ('$nombre', '$correo', '$contrasena')";

if ($conn->query($sql)) {

    $id = $conn->insert_id; // 🔥 ID generado

    // 🔥 2. Insertar en tabla según tipo
    if ($tipo == "admin") {
        $conn->query("INSERT INTO administrador (id_usuario) VALUES ($id)");
    } 
    else if ($tipo == "jefe") {
        $conn->query("INSERT INTO jefe_departamento (id_usuario) VALUES ($id)");
    } 
    else if ($tipo == "profesor") {
        $conn->query("INSERT INTO profesor (id_usuario) VALUES ($id)");
    } 
    else if ($tipo == "alumno") {
        $conn->query("INSERT INTO alumno (id_usuario) VALUES ($id)");
    }

    echo json_encode([
        "status" => "success",
        "id" => $id
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "error" => $conn->error
    ]);
}
?>