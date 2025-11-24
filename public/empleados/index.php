<?php
require_once __DIR__ . '/../../src/Repositories/EmpleadoRepository.php';

$repository = new EmpleadoRepository();
$empleados = $repository->readAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Sistema Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        .header-nav a:hover {
            color: #f0f0f0;
        }
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-add:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a91 100%);
            color: white;
        }
        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-huella {
            font-size: 0.9em;
            padding: 5px 10px;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Empleados</h2>
            <a href="nuevo.php" class="btn btn-add">
                <i class="bi bi-plus-circle"></i> Agregar nuevo
            </a>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre o número de empleado...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Número de Empleado</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Huella</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="empleadosTable">
                    <?php foreach ($empleados as $index => $empleado): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($empleado->getNumEmpleado()) ?></td>
                        <td><?= htmlspecialchars($empleado->getNombre()) ?></td>
                        <td><?= htmlspecialchars($empleado->getApellido()) ?></td>
                        <td>
                            <?php if ($empleado->getBiometrico()): ?>
                                <span class="badge bg-success badge-huella">Sí</span>
                            <?php else: ?>
                                <span class="badge bg-danger badge-huella">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar.php?id=<?= $empleado->getIdEmpleado() ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const empleadosTable = document.getElementById('empleadosTable');
        const allRows = Array.from(empleadosTable.querySelectorAll('tr'));

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            allRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return;
                
                const numEmpleado = cells[1].textContent.toLowerCase();
                const nombre = cells[2].textContent.toLowerCase();
                const apellido = cells[3].textContent.toLowerCase();
                
                const matches = numEmpleado.includes(searchTerm) || 
                              nombre.includes(searchTerm) || 
                              apellido.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
            });
        });
    </script>
</body>
</html>

