    <?php
    include 'conexion.php';

    $id = $_POST['id_departamento'];
    $nombre = $_POST['nombre'];
    $area = $_POST['area'];
    $id_jefe = $_POST['id_jefe'];

    // 🔥 1. LIMPIAR JEFE ANTERIOR
    $conn->query("UPDATE jefe_departamento 
                SET id_departamento = NULL 
                WHERE id_departamento = $id");

    // 🔥 2. ACTUALIZAR DEPARTAMENTO
    $conn->query("UPDATE departamento 
                SET nombre='$nombre', area='$area', id_jefe='$id_jefe'
                WHERE id_departamento=$id");

    // 🔥 3. ASIGNAR NUEVO JEFE
    $conn->query("UPDATE jefe_departamento 
                SET id_departamento=$id 
                WHERE id_usuario=$id_jefe");

    echo json_encode(["status"=>"success"]); 
    ?>