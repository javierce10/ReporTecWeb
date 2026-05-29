<?php
include 'conexion.php';

// Añadimos u.activo justo después del correo
$sql = "SELECT u.id_usuario, u.nombre, u.correo, u.activo,
CASE
    WHEN a.id_usuario IS NOT NULL THEN 'admin'
    WHEN j.id_usuario IS NOT NULL THEN 'jefe'
    WHEN p.id_usuario IS NOT NULL THEN 'profesor'
END as tipo
FROM usuario u
LEFT JOIN administrador a ON u.id_usuario = a.id_usuario
LEFT JOIN jefe_departamento j ON u.id_usuario = j.id_usuario
LEFT JOIN profesor p ON u.id_usuario = p.id_usuario";

$result = $conn->query($sql); 

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>