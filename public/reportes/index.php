<?php
require_once __DIR__ . '/../../src/Repositories/EventoRepository.php';

$repository = new EventoRepository();
$ultimosEventos = $repository->getLast10();
$estadisticasSemana = $repository->getWeeklyStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Sistema Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .header-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header-nav">
        <div class="container">
            <a href="../index.php"><i class="bi bi-house-fill"></i> Inicio</a>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4"><i class="bi bi-graph-up-arrow"></i> Reportes</h2>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Número</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Hora</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimosEventos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay eventos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimosEventos as $evento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($evento['NumEmpleado']) ?></td>
                                    <td><?= htmlspecialchars($evento['Nombre']) ?></td>
                                    <td><?= htmlspecialchars($evento['Apellido']) ?></td>
                                    <td><?= htmlspecialchars($evento['Hora']) ?></td>
                                    <td><?= htmlspecialchars($evento['Fecha']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">Usuarios con registros - Semana Actual</h5>
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preparar datos para la gráfica
        const estadisticas = <?= json_encode($estadisticasSemana) ?>;
        
        // Crear array con todos los días de la semana (Lunes a Sábado)
        const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const datosPorDia = {};
        
        // Inicializar todos los días en 0
        diasSemana.forEach((dia, index) => {
            datosPorDia[index] = 0;
        });
        
        // Llenar con datos reales
        estadisticas.forEach(stat => {
            const fecha = new Date(stat.Fecha);
            const diaSemana = fecha.getDay(); // 0 = Domingo, 1 = Lunes, etc.
            // Convertir: Lunes = 0, Martes = 1, ..., Sábado = 5
            const indice = diaSemana === 0 ? 6 : diaSemana - 1; // Si es domingo, ponerlo como índice 6 (no se muestra)
            if (indice >= 0 && indice <= 5) {
                datosPorDia[indice] = parseInt(stat.TotalUsuarios);
            }
        });
        
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: diasSemana,
                datasets: [{
                    label: 'Total de Usuarios',
                    data: Object.values(datosPorDia),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>

