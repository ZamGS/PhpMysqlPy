<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checador - Sistema Checador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../uareu/app.css" type="text/css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .checador-container {
            text-align: center;
            width: 100%;
            max-width: 800px;
            padding: 40px;
        }
        .btn-selector {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .clock-display {
            font-size: 120px;
            font-weight: bold;
            margin: 40px 0;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Courier New', monospace;
        }
        .user-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 30px auto;
            object-fit: cover;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-text {
            font-size: 36px;
            font-weight: bold;
            margin: 20px 0;
        }
        .instruction-text {
            font-size: 24px;
            margin: 20px 0;
            opacity: 0.9;
        }
        .employee-info {
            font-size: 28px;
            margin: 10px 0;
        }
        #status {
            margin: 20px 0;
            font-size: 18px;
        }
        .modal-content {
            color: #333;
        }
    </style>
</head>
<body>
    <button class="btn btn-light btn-selector" data-bs-toggle="modal" data-bs-target="#readerModal">
        <i class="bi bi-usb-plug"></i> Seleccionar lector
    </button>

    <div class="checador-container">
        <div class="clock-display" id="clockDisplay"></div>
        
        <div class="user-image" id="userImage">
            <i class="bi bi-person-circle" style="font-size: 150px; opacity: 0.5;"></i>
        </div>
        
        <div class="welcome-text" id="welcomeText">Bienvenidos</div>
        <div class="instruction-text" id="instructionText">Coloque su dedo en el lector para registrar evento</div>
        
        <div id="status"></div>
    </div>

    <!-- Modal para seleccionar lector -->
    <div class="modal fade" id="readerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Lector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h4>Select Reader :</h4>
                    <select class="form-control" id="readersDropDown" onchange="selectChangeEvent()"></select>
                    <div id="readerDivButtons" style="margin-top: 10px;">
                        <input type="button" class="btn btn-primary" id="refreshList" value="Refresh List" onclick="readersDropDownPopulate(false)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="aceptarLector()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor oculto para captura, ya que en esta vista no son necesarioas per se incluyen para evitar errores -->
    <div id="content-capture" style="display: none;">

        <form name="myForm" style="border:solid grey;padding:10px; background: white;">
            <b>Acquire Formats :</b>
            <input class="form-check-input" type="checkbox" name="PngImage" value="4" checked="true" disabled>
            <label class="form-check-label">PNG</label>
        </form>

        <div id="imagediv"></div>

        <!-- 2. Info (Quality) -->
        <div id="Scores" class="mb-3">
            <label class="form-label fw-bold">Scan Quality:</label>
            <input type="text" id="qualityInputBox" class="form-control text-center bg-light" readonly>
        </div>

        <div class="row">
            <div id="imageGallery" class="mt-3"></div>
            <div id="deviceInfo" class="mt-3"></div>
        </div> 
    </div>

    <script src="../../uareu/lib/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../uareu/scripts/es6-shim.js"></script>
    <script src="../../uareu/scripts/websdk.client.bundle.min.js"></script>
    <script src="../../uareu/scripts/fingerprint.sdk.min.js"></script>
    <script src="../../uareu/app.js"></script>
    <script>
        let isCapturing = false;
        let resetTimeout = null;
        let selectedReader = null;

        // Actualizar reloj
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clockDisplay').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Inicializar lector automáticamente
        window.addEventListener('load', function() {
            setTimeout(() => {
                readersDropDownPopulate(false);
                // Seleccionar primer lector USB automáticamente
                setTimeout(() => {
                    const dropdown = document.getElementById('readersDropDown');
                    if (dropdown.options.length > 0) {
                        dropdown.selectedIndex = 0;
                        selectChangeEvent();
                        startAutoCapture();
                    }
                }, 1000);
            }, 500);
        });

        function aceptarLector() {
            const dropdown = document.getElementById('readersDropDown');
            if (dropdown.value) {
                selectedReader = dropdown.value;
                selectChangeEvent();
                const modal = bootstrap.Modal.getInstance(document.getElementById('readerModal'));
                modal.hide();
                startAutoCapture();
            }
        }

        function startAutoCapture() {
            if (isCapturing) return;
            isCapturing = true;
            onStart();
        }

        function stopAutoCapture() {
            if (!isCapturing) return;
            isCapturing = false;
            onStop();
        }

        // Interceptar sampleAcquired para procesamiento automático
        var originalSampleAcquired = sampleAcquired;
        sampleAcquired = function(s) {
            if (originalSampleAcquired) originalSampleAcquired(s);
            
            if (!isCapturing) return;
            
            // Detener captura temporalmente
            stopAutoCapture();
            
            // Procesar huella
            processFingerprint(s);
        };

        async function processFingerprint(capture) {
            try {
                document.getElementById('status').textContent = 'Procesando huella...';
                
                // Convertir a base64
                var samples = JSON.parse(capture.samples);
                var data = Fingerprint.b64UrlTo64(samples[0]);
                
                // Crear archivo temporal para enviar al API
                const base64Data = data;
                const byteCharacters = atob(base64Data);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                const blob = new Blob([byteArray], {type: 'image/png'});
                
                const formData = new FormData();
                formData.append('fingerprint', blob, 'fingerprint.png');
                
                // Llamar al API Flask
                const response = await fetch('http://127.0.0.1:5000/verify', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.match && result.best) {
                    const empleado = result.best;
                    
                    // Obtener datos completos del empleado
                    const empleadoResponse = await fetch(`../../public/api/empleados.php?id=${empleado.IdEmpleado}`);
                    const empleadoData = await empleadoResponse.json();
                    
                    if (empleadoData.success) {
                        const emp = empleadoData.data;
                        
                        // Mostrar información del empleado
                        document.getElementById('welcomeText').textContent = `${emp.Nombre} ${emp.Apellido}`;
                        document.getElementById('instructionText').textContent = `Número: ${emp.NumEmpleado}`;
                        
                        if (emp.Foto) {
                            document.getElementById('userImage').innerHTML = `<img src="../../${emp.Foto}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
                        }
                        
                        // Crear evento
                        await fetch('../../public/api/eventos.php?action=checador', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                IdEmpleado: empleado.IdEmpleado
                            })
                        });
                        
                        document.getElementById('status').textContent = 'Evento registrado correctamente';
                        
                        // Resetear después de 3 segundos
                        resetTimeout = setTimeout(() => {
                            resetDisplay();
                            startAutoCapture();
                        }, 3000);
                    }
                } else {
                    document.getElementById('status').textContent = 'Huella no reconocida. Intente nuevamente.';
                    setTimeout(() => {
                        document.getElementById('status').textContent = '';
                        startAutoCapture();
                    }, 2000);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('status').textContent = 'Error al procesar huella. Intente nuevamente.';
                setTimeout(() => {
                    document.getElementById('status').textContent = '';
                    startAutoCapture();
                }, 2000);
            }
        }

        function resetDisplay() {
            document.getElementById('welcomeText').textContent = 'Bienvenidos';
            document.getElementById('instructionText').textContent = 'Coloque su dedo en el lector para registrar evento';
            document.getElementById('userImage').innerHTML = '<i class="bi bi-person-circle" style="font-size: 150px; opacity: 0.5;"></i>';
            document.getElementById('status').textContent = '';
        }

        // Limpiar timeout si se cambia de lector
        document.getElementById('readersDropDown').addEventListener('change', function() {
            if (resetTimeout) {
                clearTimeout(resetTimeout);
                resetTimeout = null;
            }
            stopAutoCapture();
        });
    </script>
</body>
</html>

