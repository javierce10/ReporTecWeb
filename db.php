<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "reportec";

$conexion = mysqli_connect($host, $user, $pass, $db);

// Esto te sirve para tu informe como "Manejo de Excepciones"
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>