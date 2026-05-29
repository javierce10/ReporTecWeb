<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include '../conexion.php';

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

$where = " WHERE e.estado IN ('Resuelto', 'Cerrado', 'resuelto', 'cerrado') ";
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $where .= " AND i.datetime BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59' ";
}

$sql = "
    SELECT d.nombre,
        ROUND(AVG(TIMESTAMPDIFF(MINUTE, i.datetime, e.datetime)) / 60.0, 2) AS promedio_horas
    FROM incidencia i
    JOIN estatus e ON i.id_incidencia = e.id_incidencia
    JOIN tipo_incidencia ti ON i.id_tipo = ti.id_tipo
    JOIN departamento d ON ti.id_departamento = d.id_departamento
    $where
    GROUP BY d.id_departamento, d.nombre
    HAVING promedio_horas IS NOT NULL
    ORDER BY promedio_horas ASC
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