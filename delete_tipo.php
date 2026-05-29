<?php
include 'conexion.php';

$id_tipo = $_POST['id_tipo'];

$sql = "DELETE FROM tipo_incidencia WHERE id_tipo = '$id_tipo'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "mensaje" => $conn->error]);
}
$conn->close();
?>