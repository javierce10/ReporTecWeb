<?php
include 'conexion.php';

$sql = "SELECT t.id_tipo, t.nombre, t.descripcion, t.id_departamento, d.nombre AS nombre_departamento
        FROM tipo_incidencia t
        LEFT JOIN departamento d ON t.id_departamento = d.id_departamento
        ORDER BY d.nombre ASC, t.nombre ASC";

$result = $conn->query($sql);

$tipos = [];
while ($row = $result->fetch_assoc()) {
    $tipos[] = $row;
}

echo json_encode($tipos);
$conn->close();
?>