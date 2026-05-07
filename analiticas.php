<?php
// Usamos la conexión $conexion de panel_admin.php

// 1. Datos para "Incidencias más reportadas" (Top Tipos)
$query_tipos = "SELECT ti.nombre, COUNT(i.id_incidencia) as total 
                FROM tipo_incidencia ti
                LEFT JOIN incidencia i ON ti.id_tipo = i.id_tipo
                GROUP BY ti.id_tipo 
                ORDER BY total DESC LIMIT 5";
$res_tipos = mysqli_query($conexion, $query_tipos);
$labels_tipos = []; $data_tipos = [];
while($r = mysqli_fetch_assoc($res_tipos)){
    $labels_tipos[] = $r['nombre'];
    $data_tipos[] = $r['total'];
}

// 2. Datos para "Departamentos con más reportes"
$query_deps = "SELECT d.nombre, COUNT(i.id_incidencia) as total 
               FROM departamento d
               INNER JOIN profesor p ON d.id_departamento = p.id_departamento
               INNER JOIN incidencia i ON p.id_usuario = i.id_usuario
               GROUP BY d.id_departamento 
               ORDER BY total DESC LIMIT 5";
$res_deps = mysqli_query($conexion, $query_deps);
$labels_deps = []; $data_deps = [];
while($r = mysqli_fetch_assoc($res_deps)){
    $labels_deps[] = $r['nombre'];
    $data_deps[] = $r['total'];
}

// 3. Resumen de Estados (Estatus actual)
$query_status = "SELECT estado, COUNT(*) as total FROM estatus GROUP BY estado";
$res_status = mysqli_query($conexion, $query_status);
$status_counts = ['Pendiente' => 0, 'En Proceso' => 0, 'Resuelto' => 0];
while($r = mysqli_fetch_assoc($res_status)){
    $status_counts[$r['estado']] = $r['total'];
}
?>

<!-- Importar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="gestion-container">
    <div class="header-section">
        <h2>Panel de Analíticas e Indicadores</h2>
        <p style="color: #64748b;">Monitoreo en tiempo real del sistema Reportec</p>
    </div>

    <!-- Tarjetas de Resumen Rápido -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 15px; border-left: 5px solid #3b82f6; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">INCIDENCIAS TOTALES</span>
            <h3 style="font-size: 2rem; margin: 5px 0; color: #1e293b;"><?php echo array_sum($status_counts); ?></h3>
        </div>
        <div style="background: white; padding: 20px; border-radius: 15px; border-left: 5px solid #ef4444; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">PENDIENTES</span>
            <h3 style="font-size: 2rem; margin: 5px 0; color: #ef4444;"><?php echo $status_counts['Pendiente']; ?></h3>
        </div>
        <div style="background: white; padding: 20px; border-radius: 15px; border-left: 5px solid #10b981; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">RESUELTAS</span>
            <h3 style="font-size: 2rem; margin: 5px 0; color: #10b981;"><?php echo $status_counts['Resuelto']; ?></h3>
        </div>
    </div>

    <!-- Gráficas Principales -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px;">
        
        <!-- Gráfica de Barras: Departamentos -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <h4 style="margin-bottom: 20px; color: #1e293b;"><i class="fas fa-chart-bar"></i> Reportes por Departamento</h4>
            <canvas id="chartDepartamentos"></canvas>
        </div>

        <!-- Gráfica de Dona: Tipos de Incidencia -->
        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <h4 style="margin-bottom: 20px; color: #1e293b;"><i class="fas fa-chart-pie"></i> Tipos de Incidencias Frecuentes</h4>
            <canvas id="chartTipos"></canvas>
        </div>

    </div>
</div>

<script>
// Configuración Gráfica Departamentos
const ctxDep = document.getElementById('chartDepartamentos').getContext('2d');
new Chart(ctxDep, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels_deps); ?>,
        datasets: [{
            label: 'Número de Reportes',
            data: <?php echo json_encode($data_deps); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

// Configuración Gráfica Tipos (Dona)
const ctxTipo = document.getElementById('chartTipos').getContext('2d');
new Chart(ctxTipo, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($labels_tipos); ?>,
        datasets: [{
            data: <?php echo json_encode($data_tipos); ?>,
            backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
            ],
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>