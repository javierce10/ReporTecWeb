<?php
// reportes_recibidos.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "reportec");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$id_usuario = intval($_GET['id_usuario'] ?? 0);

if ($id_usuario === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'id_usuario requerido']);
    exit;
}

// 1. Obtener el departamento del jefe
$stmt = $conn->prepare("SELECT id_departamento FROM jefe_departamento WHERE id_usuario = ?");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No es jefe de ningún departamento']);
    exit;
}

$jefe = $result->fetch_assoc();
$id_departamento = $jefe['id_departamento'];
$stmt->close();

// 2. Obtener todas las incidencias y adjuntar su imagen de evidencia si existe
$sql = "
    SELECT
        i.id_incidencia,
        i.descripcion,
        i.datetime,
        u.nombre        AS nombre_usuario,
        a.nombre        AS nombre_aula,
        a.edificio      AS edificio,
        ti.nombre       AS tipo_incidencia,
        ev.imagen       AS foto_evidencia,
        COALESCE(e.estado, 'Pendiente') AS estado
    FROM incidencia i
    JOIN tipo_incidencia ti ON i.id_tipo    = ti.id_tipo
    JOIN usuario         u  ON i.id_usuario = u.id_usuario
    LEFT JOIN aula       a  ON i.id_aula    = a.id_aula
    LEFT JOIN evidencia  ev ON ev.id_incidencia = i.id_incidencia
    LEFT JOIN estatus    e  ON e.id_incidencia = i.id_incidencia
    WHERE ti.id_departamento = ?
    ORDER BY i.datetime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_departamento);
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