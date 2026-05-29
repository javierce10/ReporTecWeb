<?php
include 'conexion.php';

$id = $_POST['id_departamento'];

$conn->query("DELETE FROM departamento WHERE id_departamento=$id");

echo json_encode(["status"=>"success"]);
?>