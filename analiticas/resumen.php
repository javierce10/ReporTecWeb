<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include '../conexion.php';

// Captura de fechas enviadas por Flutter
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Construcción de condiciones WHERE dinámicas basándonos en fechas
$whereIncidencia = " WHERE 1=1 ";
$whereEstatus = " WHERE e.estado IN ('Resuelto', 'Cerrado', 'resuelto', 'cerrado') ";

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $whereIncidencia .= " AND datetime BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59' ";
    $whereEstatus .= " AND i.datetime BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59' ";
}

$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM incidencia $whereIncidencia");

$resueltasQuery = $conn->query("
    SELECT COUNT(DISTINCT e.id_incidencia) AS total
    FROM estatus e
    JOIN incidencia i ON e.id_incidencia = i.id_incidencia
    $whereEstatus
");

if (!$totalQuery || !$resueltasQuery) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$total = $totalQuery->fetch_assoc()['total'];
$resueltas = $resueltasQuery->fetch_assoc()['total'];

echo json_encode([
    "total" => (int)$total,
    "resueltas" => (int)$resueltas
]);

$conn->close();
?>