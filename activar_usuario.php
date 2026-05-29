<?php
include 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Validamos que se reciba el ID del usuario y el estado deseado
if (isset($_POST['id_usuario']) && isset($_POST['estado'])) {
    
    $id = intval($_POST['id_usuario']);
    $estado = intval($_POST['estado']); // Captura 1 o 0 desde el cliente

    // Validamos que el estado sea estrictamente 0 o 1 por seguridad
    if ($estado !== 0 && $estado !== 1) {
        echo json_encode(["status" => "error", "mensaje" => "Estado inválido"]);
        exit;
    }

    try {
        // Cambiamos el '1' fijo por un signo de interrogación '?' para la variable $estado
        $stmt = $conn->prepare("UPDATE usuario SET activo = ? WHERE id_usuario = ?");
        $stmt->bind_param("ii", $estado, $id); // Pasamos 'ii' porque ambos son enteros (int)
        
        if ($stmt->execute()) {
            // Personalizamos el mensaje de éxito según el estado enviado
            $textoMensaje = ($estado === 1) ? "Usuario activado correctamente" : "Usuario inactivado correctamente";
            echo json_encode(["status" => "success", "mensaje" => $textoMensaje]);
        } else {
            echo json_encode(["status" => "error", "mensaje" => "No se pudo actualizar el estado"]);
        }
        
        $stmt->close();

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "mensaje" => "Error en el servidor: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "mensaje" => "Faltan parámetros (ID de usuario o Estado)"
    ]);
}
?>