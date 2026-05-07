<?php
// Iniciar la sesión para poder identificarla
session_start();

// Desvincular todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también hay que borrar la cookie de sesión.
// Nota: Esto es opcional pero recomendado para una limpieza total.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al usuario al formulario de inicio de sesión (index.php)
header("Location: index.php");
exit();
?>