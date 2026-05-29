<?php
session_start();
header('Content-Type: application/json');

// Habilitamos el reporte de errores para que MySQLi nos avise si falla la conexión
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Si la conexión falla, se irá directo al bloque 'catch' de abajo
    require_once 'conexion.php';

    $correo    = $_POST['correo']    ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (!$correo || !$contrasena) {
        echo json_encode(['status' => 'error', 'message' => 'Campos vacíos']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? AND contrasena = ? AND activo = 1");
    $stmt->bind_param('ss', $correo, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $id   = $user['id_usuario'];

        // Detectar tipo de usuario
        $tipo = 'desconocido';
        if ($conn->query("SELECT 1 FROM administrador WHERE id_usuario = $id")->num_rows > 0)       $tipo = 'admin';
        elseif ($conn->query("SELECT 1 FROM jefe_departamento WHERE id_usuario = $id")->num_rows > 0) $tipo = 'jefe';
        elseif ($conn->query("SELECT 1 FROM profesor WHERE id_usuario = $id")->num_rows > 0)          $tipo = 'profesor';

        // Guardar sesión
        $_SESSION['id_usuario'] = $id;
        $_SESSION['nombre']     = $user['nombre'];
        $_SESSION['correo']     = $user['correo'];
        $_SESSION['tipo']       = $tipo;

        $redirects = [
            'admin'   => 'vistas/admin.php',
            'jefe'    => 'vistas/jefe.php',
            'profesor' => 'vistas/profesor.php',
            'desconocido' => 'index.php'
        ];

        echo json_encode([
            'status'   => 'success',
            'redirect' => $redirects[$tipo] ?? 'index.php'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas o usuario inactivo']);
    }

} catch (Exception $e) {
    // Si el servidor falla (ej. error de conexión a la Base de Datos), atrapamos el error aquí
    // y devolvemos una respuesta amigable en lugar de un error 500
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor de Base de Datos: ' . $e->getMessage()
    ]);
}
?>