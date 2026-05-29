<?php
include 'conexion.php';

$sql = "SELECT id_aula, nombre, edificio 
        FROM aula 
        ORDER BY edificio ASC, nombre ASC";

$result = $conn->query($sql);

$aulas = [];
while ($row = $result->fetch_assoc()) {
    $aulas[] = $row;
}

echo json_encode($aulas);
$conn->close();
?>