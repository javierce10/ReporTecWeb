<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include '../conexion.php';

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

$where = " WHERE 1=1 ";
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $where .= " AND i.datetime BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59' ";
}

$sql = "
    SELECT ti.nombre, COUNT(i.id_incidencia) AS total
    FROM incidencia i
    JOIN tipo_incidencia ti ON i.id_tipo = ti.id_tipo
    $where
    GROUP BY ti.id_tipo, ti.nombre
    ORDER BY total DESC
    LIMIT 8
";

$result = $conn->query($sql);
if (!$result) { 
    echo json_encode(["error" => $conn->error]); 
    exit; 
}

$data = [];
while ($row = $result->fetch_assoc()) { 
    $data[] = $row; 
}

echo json_encode($data);
$conn->close();
?>