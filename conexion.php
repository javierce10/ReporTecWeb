<?php
$host = "localhost";
//$user = "u204741856_userreportec";
//$pass = "UserReporTec123";
//$db   = "u204741856_reportec";

$user = "root";
$pass = "";
$db   = "reportec";

$conn = mysqli_connect($host, $user, $pass, $db);

// Esto te sirve para tu informe como "Manejo de Excepciones"
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>