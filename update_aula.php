<?php
include 'conexion.php';

$id = $_POST['id_aula'];
$nombre = $_POST['nombre'];
$edificio = $_POST['edificio'];

$sql = "UPDATE aula 
        SET nombre='$nombre', edificio='$edificio' 
        WHERE id_aula='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error"]);
}
?>