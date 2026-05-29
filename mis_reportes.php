<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include 'conexion.php';

$id_usuario = intval($_GET['id_usuario'] ?? 0);

if ($id_usuario === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'id_usuario requerido']);
    exit;
}

$sql = "
    SELECT
        i.id_incidencia,
        i.descripcion,
        i.datetime,
        a.nombre   AS nombre_aula,
        a.edificio AS edificio,
        ti.nombre  AS tipo_incidencia,
        COALESCE(e.estado, 'Pendiente') AS estado
    FROM incidencia i
    LEFT JOIN aula            a  ON i.id_aula  = a.id_aula
    LEFT JOIN tipo_incidencia ti ON i.id_tipo   = ti.id_tipo
    LEFT JOIN estatus         e  ON e.id_incidencia = i.id_incidencia
    WHERE i.id_usuario = ?
    ORDER BY i.datetime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();

$result = $stmt->get_result();
$reportes = [];

while ($row = $result->fetch_assoc()) {
    $reportes[] = $row;
}

echo json_encode($reportes);

$stmt->close();
$conn->close();
?> 