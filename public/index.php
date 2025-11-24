<?php
/**
 * Página principal del sistema checador
 * Muestra tiles para Empleados y Reportes
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .tile-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .tile-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .tile-icon {
            font-size: 80px;
            margin-bottom: 20px;
            color: #667eea;
        }
        .tile-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .tile-description {
            color: #666;
            font-size: 16px;
        }
        .container-custom {
            max-width: 900px;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="tile-card" onclick="window.location.href='empleados/index.php'">
                    <i class="bi bi-people-fill tile-icon"></i>
                    <div class="tile-title">Empleados</div>
                    <div class="tile-description">Gestionar empleados y huellas dactilares</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tile-card" onclick="window.location.href='reportes/index.php'">
                    <i class="bi bi-graph-up-arrow tile-icon"></i>
                    <div class="tile-title">Reportes</div>
                    <div class="tile-description">Ver eventos y estadísticas de asistencia</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

