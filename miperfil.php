<?php
// Obtenemos los datos del usuario de la sesión actual
// Asumiendo que guardaste el ID en $_SESSION['id_usuario']

$id_logueado = $_SESSION['id_usuario'];

// 1. Consulta para obtener datos básicos
$query_user = "SELECT nombre, correo FROM usuario WHERE id_usuario = '$id_logueado'";
$res_user = mysqli_query($conexion, $query_user);
$datos_user = mysqli_fetch_assoc($res_user);

// 2. Lógica para determinar el ROL (según tu estructura de tablas relacionales)
$rol_display = "Usuario"; // Default

$check_admin = mysqli_query($conexion, "SELECT id_usuario FROM administrador WHERE id_usuario = '$id_logueado'");
$check_jefe = mysqli_query($conexion, "SELECT id_usuario FROM jefe_departamento WHERE id_usuario = '$id_logueado'");
$check_profe = mysqli_query($conexion, "SELECT id_usuario FROM profesor WHERE id_usuario = '$id_logueado'");

if(mysqli_num_rows($check_admin) > 0) $rol_display = "Administrador";
elseif(mysqli_num_rows($check_jefe) > 0) $rol_display = "Jefe de Departamento";
elseif(mysqli_num_rows($check_profe) > 0) $rol_display = "Profesor / Personal";

// 3. Iniciales para la foto de perfil (Si no hay imagen)
$nombres = explode(" ", $datos_user['nombre']);
$iniciales = strtoupper(substr($nombres[0], 0, 1) . (isset($nombres[1]) ? substr($nombres[1], 0, 1) : ""));
?>

<div class="gestion-container">
    <div class="profile-card" style="background: white; max-width: 600px; margin: 40px auto; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        
        <!-- Banner Superior -->
        <div style="background: linear-gradient(135deg, var(--primary-color), #4f46e5); height: 120px; position: relative;"></div>
        
        <!-- Contenido del Perfil -->
        <div style="padding: 0 40px 40px 40px; text-align: center; position: relative;">
            
            <!-- Foto de Perfil / Avatar -->
            <div style="width: 120px; height: 120px; background: #f1f5f9; border: 5px solid white; border-radius: 50%; margin: -60px auto 15px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--primary-color); font-weight: bold; overflow: hidden; position: relative; cursor: pointer;" onclick="document.getElementById('inputFoto').click()">
                <?php 
                // Aquí podrías poner un IF para mostrar la imagen si existe en una carpeta 'uploads/'
                echo $iniciales; 
                ?>
                <!-- Overlay de edición -->
                <div style="position: absolute; bottom: 0; width: 100%; background: rgba(0,0,0,0.4); color: white; font-size: 0.7rem; padding: 2px 0;">
                    <i class="fas fa-camera"></i>
                </div>
            </div>

            <h2 style="margin: 0; color: #1e293b; font-size: 1.6rem;"><?php echo $datos_user['nombre']; ?></h2>
            <span class="badge <?php echo strtolower(str_replace(' ', '-', $rol_display)); ?>" style="margin-top: 8px; padding: 5px 15px;">
                <i class="fas fa-id-badge"></i> <?php echo $rol_display; ?>
            </span>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #e2e8f0;">

            <!-- Información Detallada -->
            <div style="text-align: left; display: grid; gap: 20px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="color: var(--primary-color); background: #eff6ff; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.8rem; color: #64748b; font-weight: 600;">CORREO ELECTRÓNICO</p>
                        <p style="margin: 0; color: #1e293b; font-weight: 500;"><?php echo $datos_user['correo']; ?></p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="color: #f59e0b; background: #fffbeb; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.8rem; color: #64748b; font-weight: 600;">SEGURIDAD</p>
                        <p style="margin: 0; color: #1e293b; font-weight: 500;">Contraseña protegida</p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div style="margin-top: 40px; display: flex; gap: 10px;">
                <button class="btn-save" style="flex: 1; border-radius: 10px;">
                    <i class="fas fa-edit"></i> Editar Perfil
                </button>
                <button class="btn-action btn-delete-table" style="padding: 0 20px; border-radius: 10px; border: 1px solid #ef4444;" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Input de archivo oculto para la foto -->
<input type="file" id="inputFoto" style="display: none;" accept="image/*" onchange="subirFoto(this)">

<script>
function subirFoto(input) {
    if (input.files && input.files[0]) {
        alert("Función para subir foto seleccionada: " + input.files[0].name);
        // Aquí podrías implementar el fetch para guardar la ruta en la DB
    }
}
</script>