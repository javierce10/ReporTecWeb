<?php
include 'conexion.php';

$id_departamento = $_GET['id_departamento'] ?? 0;

// 🔥 JEFES DISPONIBLES + EL ACTUAL
$sql = "SELECT u.id_usuario, u.nombre
        FROM usuario u
        INNER JOIN jefe_departamento j ON u.id_usuario = j.id_usuario
        WHERE j.id_departamento IS NULL
        OR j.id_departamento = $id_departamento";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>