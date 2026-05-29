<?php
include 'conexion.php';

$id_tipo         = $_POST['id_tipo'];
$nombre          = $_POST['nombre'];
$descripcion     = $_POST['descripcion'];
$id_departamento = $_POST['id_departamento'];

$sql = "UPDATE tipo_incidencia SET 
        nombre = '$nombre',
        descripcion = '$descripcion',
        id_departamento = " . ($id_departamento !== "" ? "'$id_departamento'" : "NULL") . "
        WHERE id_tipo = '$id_tipo'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "mensaje" => $conn->error]);
}
$conn->close();
?>