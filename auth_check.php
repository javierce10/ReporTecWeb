<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre     = $_SESSION['nombre'];
$correo     = $_SESSION['correo'];
$tipo       = $_SESSION['tipo'];
$inicial    = strtoupper(substr($nombre, 0, 1));
?>
