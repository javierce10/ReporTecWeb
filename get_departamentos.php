<?php
include 'conexion.php'; 

// Traemos todos los departamentos y unimos con la tabla 'usuario' para obtener el nombre del jefe
$query = "SELECT d.id_departamento, d.nombre, d.area, d.id_jefe, u.nombre AS nombre_jefe 
          FROM departamento d 
          LEFT JOIN usuario u ON d.id_jefe = u.id_usuario";

$result = mysqli_query($conn, $query);
$departamentos = array();

while($row = mysqli_fetch_assoc($result)) {
    $departamentos[] = $row;
}

echo json_encode($departamentos);
?>