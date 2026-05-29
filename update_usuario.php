<?php
include 'conexion.php';

// Habilitar el reporte de errores de MySQLi para detectar cualquier fallo en las consultas
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Validar que se reciban los datos obligatorios (la contraseña ahora es opcional en la edición)
if (isset($_POST['id_usuario'], $_POST['nombre'], $_POST['correo'])) {
    
    $id = intval($_POST['id_usuario']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null; // Opcional por si no se cambia el tipo en el modal básico
    
    // Si manejas departamentos para profesores o jefes, los recibes aquí
    $id_departamento = isset($_POST['id_departamento']) && $_POST['id_departamento'] != "" ? intval($_POST['id_departamento']) : null;

    try {
        // Iniciamos una transacción para asegurar que se actualice el usuario correctamente
        $conn->begin_transaction();

        // 1. Verificar si se envió una nueva contraseña
        if (isset($_POST['contrasena']) && $_POST['contrasena'] !== "") {
            $contrasena = $conn->real_escape_string($_POST['contrasena']);
            // Se actualizan datos básicos incluyendo la nueva contraseña
            $conn->query("UPDATE usuario 
                          SET nombre='$nombre', correo='$correo', contrasena='$contrasena' 
                          WHERE id_usuario=$id");
        } else {
            // Se actualiza omitiendo la contraseña para mantener la actual
            $conn->query("UPDATE usuario 
                          SET nombre='$nombre', correo='$correo' 
                          WHERE id_usuario=$id");
        }

        // 2. Modificación de roles (Opcional: solo si envías el parámetro 'tipo' desde el frontend)
        if ($tipo) {
            $conn->query("DELETE FROM administrador WHERE id_usuario = $id");
            $conn->query("DELETE FROM jefe_departamento WHERE id_usuario = $id");
            $conn->query("DELETE FROM profesor WHERE id_usuario = $id");

            if ($tipo == "admin" || $tipo == "administrador") {
                $conn->query("INSERT INTO administrador (id_usuario) VALUES ($id)");
            } 
            else if ($tipo == "jefe") {
                $conn->query("INSERT INTO jefe_departamento (id_usuario, id_departamento) VALUES ($id, " . ($id_departamento ?? "NULL") . ")");
            } 
            else if ($tipo == "profesor") {
                $conn->query("INSERT INTO profesor (id_usuario, id_departamento) VALUES ($id, " . ($id_departamento ?? "NULL") . ")");
            }
        }

        // Si todo se ejecutó sin errores, guardamos los cambios en la base de datos
        $conn->commit();

        echo json_encode(["status" => "success", "mensaje" => "Usuario actualizado correctamente"]);

    } catch (Exception $e) {
        // Si algo falló deshacemos todo
        $conn->rollback();
        echo json_encode([
            "status" => "error",
            "mensaje" => "Error al actualizar el usuario: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "mensaje" => "Faltan datos requeridos en la petición"
    ]);
}
?>