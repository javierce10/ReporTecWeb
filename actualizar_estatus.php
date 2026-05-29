<?php
// actualizar_estatus.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = new mysqli("localhost", "root", "", "reportec");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$id_incidencia  = intval($_POST['id_incidencia']  ?? 0);
$id_responsable = intval($_POST['id_responsable'] ?? 0);
$estado         = $_POST['estado'] ?? '';

if ($id_incidencia === 0 || $id_responsable === 0 || $estado === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Verificar si ya existe un estatus para esta incidencia
$check = $conn->prepare("SELECT id_estatus FROM estatus WHERE id_incidencia = ?");
$check->bind_param('i', $id_incidencia);
$check->execute();
$result = $check->get_result();
$check->close();

if ($result->num_rows > 0) {
    // Actualizar estatus existente
    $stmt = $conn->prepare("
        UPDATE estatus 
        SET estado = ?, datetime = NOW(), id_responsable = ?
        WHERE id_incidencia = ?
    ");
    $stmt->bind_param('sii', $estado, $id_responsable, $id_incidencia);
} else {
    // Crear nuevo estatus
    $stmt = $conn->prepare("
        INSERT INTO estatus (estado, datetime, id_incidencia, id_responsable)
        VALUES (?, NOW(), ?, ?)
    ");
    $stmt->bind_param('sii', $estado, $id_incidencia, $id_responsable);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'mensaje' => $conn->error]);
}

$stmt->close();
$conn->close();
?>