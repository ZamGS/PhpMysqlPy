<?php
/**
 * Página para crear nuevo empleado
 */
?>
<!DOCTYPE html>
<html lang="es">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Nuevo Empleado - Sistema Checador</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
          <h2 class="mb-4"><i class="bi bi-person-plus"></i> Nuevo Empleado</h2>
          <form id="empleadoForm" enctype="multipart/form-data">
              <input type="hidden" name="fingerprint_data" id="fingerprintData">
              
              <div class="row">
                  <div class="col-md-8">
                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label class="form-label">Nombre *</label>
                              <input type="text" class="form-control" name="Nombre" required>
                          </div>
                          <div class="col-md-6">
                              <label class="form-label">Apellido *</label>
                              <input type="text" class="form-control" name="Apellido" required>
                          </div>
                      </div>

                      <div class="row mb-3">
                          <div class="col-md-6">
                              <label class="form-label">Número de Empleado *</label>
                              <input type="text" class="form-control" name="NumEmpleado" maxlength="10" required>
                          </div>
                          <div class="col-md-6">
                              <label class="form-label">Sexo *</label>
                              <select class="form-select" name="Sexo" required>
                                  <option value="">Seleccione...</option>
                                  <option value="M">Masculino</option>
                                  <option value="F">Femenino</option>
                              </select>
                          </div>
                      </div>

                      <div class="row mb-3">
                          <div class="col-md-12">
                              <button type="button" class="btn btn-huella-capturar" data-bs-toggle="modal" data-bs-target="#fingerprintModal">
                                  <i class="bi bi-fingerprint"></i> Capturar huella
                              </button>
                              
                              <button type="button" class="btn btn-primary ms-2" onclick="guardarEmpleado()">
                                  <i class="bi bi-save"></i> Guardar
                              </button>
                          </div>
                      </div>
                  </div>

                  <div class="col-md-4">
                      <div class="photo-container">
                          <img id="photoPreview" src="" 
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
            
            <!-- Modal Header -->
            <div class="modal-header">
              <h5 class="modal-title">Capturar Huella Dactilar</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
              
              <!-- 1. Reader Selection -->
              <div id="content-reader" class="mb-3">
                <h4>Select Reader :</h4>
                <select class="form-select" id="readersDropDown" onchange="selectChangeEvent()"></select>
                
                <div id="readerDivButtons" class="mt-3 text-center">
                  <div class="row g-2 justify-content-center">
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" id="refreshList" onclick="readersDropDownPopulate(false)">
                        Refresh List
                      </button>
                    </div>
                    <!-- <div class="col-auto">
                      <button type="button" class="btn btn-primary" id="capabilities" 
                              data-bs-toggle="modal" data-bs-target="#myModal" onclick="populatePopUpModal()">
                        Capabilities
                      </button>
                    </div>-->
                  </div>
                </div>
              </div>

              <!-- 2. Info (Quality) -->
              <div id="Scores" class="mb-3">
                <label class="form-label fw-bold">Scan Quality:</label>
                <input type="text" id="qualityInputBox" class="form-control text-center bg-light" readonly>
              </div>

              <!-- 3. Control (Image + Formats) -->
              <div id="content-capture">
                <div id="status" class="mb-2"></div>

                <div class="row">
                  <div id="saveAndFormats" class="border p-3 bg-white">
                      
                    </div>
                </div>

                <div class="row justify-content-center">
                  <!-- Imagen -->
                  <div class="col-10">
                    <form name="myForm" style="border:solid grey;padding:10px; background: white;">
                      <b>Acquire Formats :</b>
                      <input class="form-check-input" type="checkbox" name="PngImage" value="4" checked="true" disabled>
                      <label class="form-check-label">PNG</label>
                    </form>
                    <div class="border rounded p-3 text-center bg-light" style="min-height: 300px; display:flex; align-items:center; justify-content:center;">
                      <div id="imagediv"></div>
                    </div>
                  </div>
                </div>

                <!-- <div class="row">
                  <div id="imageGallery" class="mt-3"></div>
                  <div id="deviceInfo" class="mt-3"></div>
                </div> -->
                
                <!-- 4. Buttons -->
                <div class="row mt-4 text-center">
                  <div class="col">
                    <button type="button" class="btn btn-primary" id="clearButton" onclick="onClear()">Clear</button>
                  </div>
                  <div class="col">
                    <button type="button" class="btn btn-primary" id="start" onclick="onStart()">Start</button>
                  </div>
                  <div class="col">
                    <button type="button" class="btn btn-primary" id="stop" onclick="onStop()">Stop</button>
                  </div>
                </div>
              </div>

              <!-- Modal interno (Capabilities) oculto -->
              <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content" id="modalContent">
                    <div class="modal-header">
                      <h4 class="modal-title">Reader Information</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="ReaderInformationFromDropDown"></div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Modal Footer -->
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
              const formData = new FormData(document.getElementById('empleadoForm'));
              
              fetch('../../public/api/empleados.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire('Éxito', data.message || 'Empleado creado correctamente', 'success')
                          .then(() => window.location.href = 'index.php');
                  } else {
                      Swal.fire('Error', data.error || 'Error al crear empleado', 'error');
                  }
              })
              .catch(error => {
                  Swal.fire('Error', 'Error de conexión', 'error');
              });
          }

          // Inicializar lector al abrir el modal y corregir accesibilidad
          var fingerprintModal = document.getElementById('fingerprintModal');
          
          fingerprintModal.addEventListener('show.bs.modal', function() {
              this.removeAttribute('aria-hidden');
          });

          fingerprintModal.addEventListener('shown.bs.modal', function() {
              this.removeAttribute('aria-hidden');
              setTimeout(() => {
                  readersDropDownPopulate(false);
              }, 500);
          });
      </script>
  </body>
</html>

