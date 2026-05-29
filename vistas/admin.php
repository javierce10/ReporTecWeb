<?php
require_once '../auth_check.php';
if ($tipo !== 'admin') { header('Location: ../index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – ReporTec</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-solo">
                <img src="../imagenes/Logoiti.png" alt="Logo TecNM">
            </div>
            <div class="sidebar-sub" style="padding-left: 0; text-align: center;">TecNM · Campus Iguala</div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $inicial ?></div>
            <div class="sidebar-user-info">
                <strong><?= htmlspecialchars($nombre) ?></strong>
                <small>Administrador</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Gestión</div>
            <a class="nav-item active" href="admin.php"><i class="fas fa-home"></i> Panel principal</a>
            <a class="nav-item" href="gestionar_usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a class="nav-item" href="gestionar_departamentos.php"><i class="fas fa-building"></i> Departamentos</a>
            <a class="nav-item" href="gestionar_aulas.php"><i class="fas fa-door-open"></i> Aulas</a>
            <a class="nav-item" href="gestionar_tipos.php"><i class="fas fa-tags"></i> Tipos de incidencia</a>
            <div class="nav-section">Estadísticas</div>
            <a class="nav-item" href="analiticas.php"><i class="fas fa-chart-bar"></i> Analíticas</a>
            <div class="nav-section">Perfil</div>
            <a class="nav-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi perfil</a>
        </nav>

        <div class="sidebar-footer">
            <button class="btn-logout" onclick="location.href='../logout.php'">
                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
            </button>
        </div>
    </aside>

    <!-- CONTENIDO -->
    <div class="main-content">
        <div class="topbar">
            <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <span class="topbar-title">Panel de Administración</span>
            <div class="topbar-right">
                <a href="perfil.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-user"></i> <?= htmlspecialchars(explode(' ', $nombre)[0]) ?>
                </a>
            </div>
        </div>

        <div class="page-body anim">
            <div class="section-header" style="margin-bottom:28px">
                <div>
                    <div class="section-title">Hola, <?= htmlspecialchars(explode(' ', $nombre)[0]) ?> 👋</div>
                    <div class="section-sub">Panel de administración del sistema</div>
                </div>
            </div>

            <!-- TARJETAS PRINCIPALES -->
            <div class="cards-grid">
                <a href="gestionar_usuarios.php" class="dash-card theme-blue">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-title">Usuarios</div>
                    <div class="card-sub">Gestionar usuarios</div>
                </a>
                <a href="gestionar_departamentos.php" class="dash-card theme-teal">
                    <div class="card-icon"><i class="fas fa-building"></i></div>
                    <div class="card-title">Departamentos</div>
                    <div class="card-sub">Gestionar departamentos</div>
                </a>
                <a href="gestionar_aulas.php" class="dash-card theme-orange">
                    <div class="card-icon"><i class="fas fa-door-open"></i></div>
                    <div class="card-title">Aulas</div>
                    <div class="card-sub">Gestionar aulas</div>
                </a>
                <a href="gestionar_tipos.php" class="dash-card theme-purple">
                    <div class="card-icon"><i class="fas fa-tags"></i></div>
                    <div class="card-title">Tipos</div>
                    <div class="card-sub">Tipos de incidencia</div>
                </a>
                <a href="analiticas.php" class="dash-card theme-pink">
                    <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                    <div class="card-title">Analíticas</div>
                    <div class="card-sub">Ver estadísticas</div>
                </a>
            </div>

            <!-- RESUMEN RÁPIDO -->
            <div style="margin-top:32px">
                <div class="section-title" style="margin-bottom:16px;font-size:16px">Resumen del sistema</div>
                <div class="stat-grid" id="statsGrid">
                    <div class="loader"><div class="spinner"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast"></div>
<script src="../js/app.js"></script>
<script>
// Cargar estadísticas rápidas
async function cargarStats() {
    try {
        const data = await apiGet('../analiticas/resumen.php');
        const grid = document.getElementById('statsGrid');
        grid.innerHTML = `
            <div class="stat-card">
                <div class="stat-num">${data.total_incidencias ?? 0}</div>
                <div class="stat-label">Total incidencias</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:var(--green)">${data.resueltas ?? 0}</div>
                <div class="stat-label">Resueltas</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:#FF9800">${data.pendientes ?? 0}</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" style="color:#2196F3">${data.en_proceso ?? 0}</div>
                <div class="stat-label">En proceso</div>
            </div>
        `;
    } catch(e) {
        document.getElementById('statsGrid').innerHTML = '<p style="color:var(--gray);font-size:13px">No se pudieron cargar las estadísticas.</p>';
    }
}
cargarStats();
</script>
</body>
</html>
