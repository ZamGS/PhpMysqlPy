<?php
require_once __DIR__ . '/../../src/Repositories/EmpleadoRepository.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$repository = new EmpleadoRepository();
$empleado = $repository->read($id);

if (!$empleado) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado - Sistema Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../uareu/css/bootstrap-min.css">
    <link rel="stylesheet" href="../../uareu/app.css" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .photo-container {
            text-align: center;
            padding: 20px;
        }
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            border: 2px solid #ddd;
            margin-bottom: 15px;
        }
        .btn-huella-capturada {
            background-color: #28a745;
            color: white;
        }
        .btn-huella-capturar {
            background-color: #dc3545;
            color: white;
        }
        #fingerprintModal .modal-body {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="header-nav">
        <div class="container">
            <a href="index.php"><i class="bi bi-house-fill"></i> Inicio</a>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-4"><i class="bi bi-pencil"></i> Editar Empleado</h2>

        <form id="empleadoForm" enctype="multipart/form-data">
            <input type="hidden" name="IdEmpleado" value="<?= $empleado->getIdEmpleado() ?>">
            <input type="hidden" name="fingerprint_data" id="fingerprintData">
            
            <div class="row">
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="Nombre" value="<?= htmlspecialchars($empleado->getNombre()) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellido *</label>
                            <input type="text" class="form-control" name="Apellido" value="<?= htmlspecialchars($empleado->getApellido()) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Número de Empleado *</label>
                            <input type="text" class="form-control" name="NumEmpleado" value="<?= htmlspecialchars($empleado->getNumEmpleado()) ?>" maxlength="10" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sexo *</label>
                            <select class="form-select" name="Sexo" required>
                                <option value="M" <?= $empleado->getSexo() == 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= $empleado->getSexo() == 'F' ? 'selected' : '' ?>>Femenino</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <?php if ($empleado->getBiometrico()): ?>
                                <button type="button" class="btn btn-huella-capturada" data-bs-toggle="modal" data-bs-target="#fingerprintModal">
                                    <i class="bi bi-fingerprint"></i> Huella capturada
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-huella-capturar" data-bs-toggle="modal" data-bs-target="#fingerprintModal">
                                    <i class="bi bi-fingerprint"></i> Capturar huella
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-primary ms-2" onclick="guardarEmpleado()">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                            
                            <button type="button" class="btn btn-danger ms-2" onclick="eliminarEmpleado()">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="photo-container">
                        <img id="photoPreview" src="<?= $empleado->getFoto() ? '../../' . htmlspecialchars($empleado->getFoto()) : 'https://via.placeholder.com/200?text=Sin+Foto' ?>" 
                             alt="Foto del empleado" class="photo-preview">
                        <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/jpg,image/png" style="display: none;" onchange="previewPhoto(this)">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fotoInput').click()">
                            <i class="bi bi-camera"></i> Editar foto
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal para captura de huella -->
    <div class="modal fade" id="fingerprintModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Capturar Huella Dactilar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="Scores">
                        <h5>Scan Quality : <input type="text" id="qualityInputBox" size="20" style="background-color:#DCDCDC;text-align:center;" readonly></h5> 
                    </div>
                    
                    <div id="content-capture">
                        <div id="status"></div>
                        <div id="imagediv"></div>
                        
                        <div id="saveAndFormats">
                            <form name="myForm" style="border:solid grey;padding:5px;">
                                <b>Acquire Formats :</b><br>
                                <table>
                                    <tr>
                                        <td><input type="checkbox" name="PngImage" value="4" checked="true" disabled> PNG</td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                        
                        <table width="100%" style="margin-top: 20px;">
                            <tr>
                                <td style="text-align: center;">
                                    <input type="button" class="btn btn-primary" id="clearButton" value="Clear" onclick="onClear()">
                                </td>
                                <td style="text-align: center;">
                                    <input type="button" class="btn btn-primary" id="start" value="Start" onclick="onStart()">
                                </td>
                                <td style="text-align: center;">
                                    <input type="button" class="btn btn-primary" id="stop" value="Stop" onclick="onStop()">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="content-reader" style="display: none;">
                        <h4>Select Reader :</h4>
                        <select class="form-control" id="readersDropDown" onchange="selectChangeEvent()"></select>
                        <div id="readerDivButtons" style="margin-top: 10px;">
                            <input type="button" class="btn btn-primary" id="refreshList" value="Refresh List" onclick="readersDropDownPopulate(false)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="guardarHuella()">Guardar Huella</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../uareu/lib/jquery.min.js"></script>
    <script src="../../uareu/lib/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../uareu/scripts/es6-shim.js"></script>
    <script src="../../uareu/scripts/websdk.client.bundle.min.js"></script>
    <script src="../../uareu/scripts/fingerprint.sdk.min.js"></script>
    <script src="../../uareu/app.js"></script>
    <script>
        var lastFingerprintCapture = null;
        var originalSampleAcquired = sampleAcquired;
        
        sampleAcquired = function(s) {
            if (originalSampleAcquired) originalSampleAcquired(s);
            lastFingerprintCapture = s;
        };

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function guardarHuella() {
            if (!lastFingerprintCapture) {
                Swal.fire('Error', 'Por favor capture una huella primero', 'error');
                return;
            }
            
            document.querySelector('input[name="PngImage"]').checked = true;
            var format = '4';
            var samples = JSON.parse(lastFingerprintCapture.samples);
            var data = Fingerprint.b64UrlTo64(samples[0]);
            
            document.getElementById('fingerprintData').value = data;
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('fingerprintModal'));
            modal.hide();
            
            Swal.fire('Éxito', 'Huella capturada correctamente. Recuerde guardar el empleado.', 'success');
        }

        function guardarEmpleado() {
            Swal.fire({
                title: '¿Guardar cambios?',
                text: '¿Está seguro de guardar los cambios realizados?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(document.getElementById('empleadoForm'));
                    
                    fetch('../../public/api/empleados.php?id=<?= $empleado->getIdEmpleado() ?>', {
                        method: 'PUT',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Éxito', data.message || 'Empleado actualizado correctamente', 'success')
                                .then(() => window.location.href = 'index.php');
                        } else {
                            Swal.fire('Error', data.error || 'Error al actualizar empleado', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });
                }
            });
        }

        function eliminarEmpleado() {
            Swal.fire({
                title: '¿Eliminar empleado?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../../public/api/empleados.php?id=<?= $empleado->getIdEmpleado() ?>', {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Éxito', data.message || 'Empleado eliminado correctamente', 'success')
                                .then(() => window.location.href = 'index.php');
                        } else {
                            Swal.fire('Error', data.error || 'Error al eliminar empleado', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });
                }
            });
        }

        // Inicializar lector al abrir el modal
        document.getElementById('fingerprintModal').addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                readersDropDownPopulate(false);
            }, 500);
        });
    </script>
</body>
</html>

