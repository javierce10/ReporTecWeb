<?php
session_start();
include('db.php');

// Verificación de seguridad
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ReporTec - Panel Administrativo</title>
    <link rel="stylesheet" href="style_paneladmin.css">
    <!-- Iconos sugeridos: FontAwesome para una apariencia más profesional -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">

    <!-- Barra Lateral -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>ReporTec</h2>
            <span>Admin</span>
        </div>
        <ul class="nav-links">
            <li><a href="?section=perfil"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
            <li><a href="?section=usuarios"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
            <li><a href="?section=departamentos"><i class="fas fa-building"></i> Departamentos</a></li>
            <li><a href="?section=aulas"><i class="fas fa-chalkboard-teacher"></i> Aulas y Áreas</a></li>
            <li><a href="?section=reportes"><i class="fas fa-chart-bar"></i> Analíticas</a></li>
            <li class="logout">
                <a href="#" onclick="confirmarLogout()"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content">
        <header class="top-bar">
            <h3>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></h3>
        </header>

        <section class="content-area">
            <?php
            $section = isset($_GET['section']) ? $_GET['section'] : 'perfil';

            switch ($section) {
                case 'usuarios':
                    include('gesusuarios.php'); // Aquí se manda a traer la interfaz de gestión de usuarios
                    break;
                case 'departamentos':
                    include('gesdepartamentos.php');
                    break;
                case 'aulas':
                    include "gesaulas.php";
                    break;
                case 'reportes':
                    include "analiticas.php";
                    break;
                default:
                    include "miperfil.php";
                    break;
            }
            ?>
        </section>
    </main>

<script>
function confirmarLogout() {
    // Despliega la ventana de confirmación del navegador
    const respuesta = confirm("¿Estás seguro de que deseas cerrar sesión en ReporTec?");
    
    if (respuesta) {
        // Si el usuario acepta, lo redirigimos al archivo PHP que destruye la sesión
        window.location.href = "logout.php";
    }
    // Si cancela, no pasa nada y se queda en la página actual
}
</script>

</body>
</html>