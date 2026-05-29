<?php
include 'conexion.php';

// 🔥 SOLO jefes que NO tienen departamento asignado
$sql = "
SELECT u.id_usuario, u.nombre
FROM usuario u
INNER JOIN jefe_departamento jd ON u.id_usuario = jd.id_usuario
LEFT JOIN departamento d ON jd.id_usuario = d.id_jefe
WHERE d.id_jefe IS NULL
";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?> 