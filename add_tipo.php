<?php
include 'conexion.php';

$nombre          = $_POST['nombre'];
$descripcion     = $_POST['descripcion'];
$id_departamento = $_POST['id_departamento'];

$sql = "INSERT INTO tipo_incidencia (nombre, descripcion, id_departamento)
        VALUES ('$nombre', '$descripcion', " . ($id_departamento !== "" ? "'$id_departamento'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "mensaje" => $conn->error]);
}
$conn->close();
?>