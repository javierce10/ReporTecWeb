<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$area = $_POST['area'];
$id_jefe = $_POST['id_jefe'];

// 🔥 INSERTAR
$conn->query("INSERT INTO departamento (nombre, area, id_jefe)
              VALUES ('$nombre','$area','$id_jefe')");

// 🔥 OBTENER ID NUEVO
$id_departamento = $conn->insert_id;

// 🔥 ACTUALIZAR JEFE
$conn->query("UPDATE jefe_departamento 
              SET id_departamento=$id_departamento
              WHERE id_usuario=$id_jefe");

echo json_encode(["status"=>"success"]);
?> 