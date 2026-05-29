<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$edificio = $_POST['edificio'];

$sql = "INSERT INTO aula (nombre, edificio) 
        VALUES ('$nombre', '$edificio')";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode([
        "status"=>"error",
        "mensaje"=>$conn->error
    ]);
}
?> 