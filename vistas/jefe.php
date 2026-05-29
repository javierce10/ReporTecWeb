<?php
require_once '../auth_check.php';
if ($tipo !== 'jefe') { header('Location: ../index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jefe – ReporTec</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-solo">
                <img src="../imagenes/logoiti.png" alt="Logo TecNM">
            </div>
            <div class="sidebar-sub" style="padding-left: 0; text-align: center;">TecNM · Campus Iguala</div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inicial ?></div>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <small>Jefe de departamento</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Acciones</div>
            <a class="nav-item active" href="jefe.php"><i class="fas fa-home"></i> Inicio</a>
            <a class="nav-item" href="crear_reporte.php"><i class="fas fa-plus-circle"></i> Crear reporte</a>
            <a class="nav-item" href="mis_reportes.php"><i class="fas fa-clipboard-list"></i> Mis reportes</a>
            <a class="nav-item" href="reportes_recibidos.php"><i class="fas fa-inbox"></i> Reportes recibidos</a>
            <div class="nav-section">Cuenta</div>
            <a class="nav-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi perfil</a>
        </nav>
        <div class="sidebar-footer">
            <button class="btn-logout" onclick="location.href='../logout.php'">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </button>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <span class="topbar-title">Panel – Jefe de Departamento</span>
            <a href="perfil.php" class="btn btn-outline btn-sm">
                <i class="fas fa-user"></i> <?= htmlspecialchars(explode(' ', $nombre)[0]) ?>
            </a>
        </div>

        <div class="page-body anim">
            <div class="section-header" style="margin-bottom:28px">
                <div>
                    <div class="section-title">Hola, <?= htmlspecialchars(explode(' ', $nombre)[0]) ?> 👋</div>
                    <div class="section-sub">¿Qué necesitas hacer hoy?</div>
                </div>
            </div>

            <div class="cards-grid">
                <a href="crear_reporte.php" class="dash-card theme-blue">
                    <div class="card-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="card-title">Crear reporte</div>
                    <div class="card-sub">Nueva incidencia</div>
                </a>
                <a href="mis_reportes.php" class="dash-card theme-teal">
                    <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="card-title">Mis reportes</div>
                    <div class="card-sub">Ver historial</div>
                </a>
                <a href="reportes_recibidos.php" class="dash-card theme-orange">
                    <div class="card-icon"><i class="fas fa-inbox"></i></div>
                    <div class="card-title">Reportes recibidos</div>
                    <div class="card-sub">De mi departamento</div>
                </a>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
</body>
</html>
