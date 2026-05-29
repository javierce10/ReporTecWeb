<?php
include 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Validamos que al menos llegue el ID del usuario
if (isset($_POST['id_usuario'])) {
    
    $id = intval($_POST['id_usuario']);
    
    // 🛠️ CAPTURA DINÁMICA: Si Flutter manda 'activo', usamos ese valor (0 o 1). 
    // Si por alguna razón no llega, por defecto asignamos 0 (dar de baja) por seguridad.
    $nuevo_estado = isset($_POST['activo']) ? intval($_POST['activo']) : 0;

    try {
        // Ejecutamos la actualización con el estado dinámico que mandó Flutter
        $stmt = $conn->prepare("UPDATE usuario SET activo = ? WHERE id_usuario = ?");
        $stmt->bind_param("ii", $nuevo_estado, $id);
        
        if ($stmt->execute()) {
            // Personalizamos el mensaje de éxito para que coincida con la acción
            $accion = ($nuevo_estado == 1) ? "reactivado" : "dado de baja";
            echo json_encode([
                "status" => "success", 
                "mensaje" => "Usuario $accion correctamente"
            ]);
        } else {
            echo json_encode([
                "status" => "error", 
                "mensaje" => "No se pudo actualizar el estado del usuario"
            ]);
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
        "mensaje" => "Faltan datos obligatorios (id_usuario)"
    ]);
}
?>