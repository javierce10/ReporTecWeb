<?php
include 'conexion.php';

$id = $_POST['id_aula'];

$sql = "DELETE FROM aula WHERE id_aula='$id'";

if ($conn->query($sql)) {
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error"]);
}
?>