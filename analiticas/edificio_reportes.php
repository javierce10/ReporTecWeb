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
    SELECT a.edificio, COUNT(i.id_incidencia) AS total
    FROM incidencia i
    JOIN aula a ON i.id_aula = a.id_aula
    $where
    GROUP BY a.edificio
    ORDER BY total DESC
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