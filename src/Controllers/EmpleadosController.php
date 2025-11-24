<?php
require_once __DIR__ . '/../Repositories/EmpleadoRepository.php';
require_once __DIR__ . '/../Models/Empleado.php';

/**
 * Controlador de Empleados
 * Maneja las peticiones HTTP relacionadas con empleados
 */
class EmpleadosController {
    private $repository;
    private $fingerprintDir;
    private $photosDir;

    public function __construct() {
        $this->repository = new EmpleadoRepository();
        $this->fingerprintDir = __DIR__ . '/../../employees_fingerprint';
        $this->photosDir = __DIR__ . '/../../employees_photos';

        // Crear directorios si no existen
        if (!file_exists($this->fingerprintDir)) {
            mkdir($this->fingerprintDir, 0777, true);
        }
        if (!file_exists($this->photosDir)) {
            mkdir($this->photosDir, 0777, true);
        }
    }

    /**
     * Manejar peticiones
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        switch ($method) {
            case 'GET':
                if ($action === 'search') {
                    $this->search();
                } elseif (isset($_GET['id'])) {
                    $this->getById($_GET['id']);
                } else {
                    $this->getAll();
                }
                break;
            case 'POST':
                $this->create();
                break;
            case 'PUT':
                $this->update();
                break;
            case 'DELETE':
                if (isset($_GET['id'])) {
                    $this->delete($_GET['id']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }

    /**
     * Obtener todos los empleados
     */
    private function getAll() {
        header('Content-Type: application/json');
        try {
            $empleados = $this->repository->readAll();
            $data = array_map(function($e) {
                return $e->toArray();
            }, $empleados);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener empleado por ID
     */
    private function getById($id) {
        header('Content-Type: application/json');
        try {
            $empleado = $this->repository->read($id);
            if ($empleado) {
                echo json_encode(['success' => true, 'data' => $empleado->toArray()]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Empleado no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Buscar empleados
     */
    private function search() {
        header('Content-Type: application/json');
        try {
            $searchTerm = $_GET['q'] ?? '';
            $empleados = $this->repository->search($searchTerm);
            $data = array_map(function($e) {
                return $e->toArray();
            }, $empleados);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Crear nuevo empleado
     */
    private function create() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Manejar datos de formulario si vienen así
            if (empty($data)) {
                $data = $_POST;
            }

            $empleado = new Empleado($data);
            
            // Validar
            $errors = $empleado->validate();
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }

            // Verificar si ya existe el número de empleado
            $existing = $this->repository->findByNumEmpleado($empleado->getNumEmpleado());
            if ($existing) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'El número de empleado ya existe']);
                return;
            }

            // Manejar foto si se subió
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $fotoPath = $this->handlePhotoUpload($_FILES['foto'], $empleado->getNumEmpleado());
                $empleado->setFoto($fotoPath);
            }

            // Manejar huella dactilar si se proporcionó
            if (isset($data['fingerprint_data']) && !empty($data['fingerprint_data'])) {
                $fingerprintPath = $this->handleFingerprintSave($data['fingerprint_data'], $empleado->getNumEmpleado());
                $empleado->setBiometrico($fingerprintPath);
            }

            $id = $this->repository->create($empleado);
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Empleado creado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar empleado
     */
    private function update() {
        header('Content-Type: application/json');
        try {
            // Manejar datos de formulario (FormData) o JSON
            $data = [];
            if (!empty($_POST)) {
                $data = $_POST;
            } else {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                if (empty($data)) {
                    // Intentar parsear como FormData
                    parse_str($input, $data);
                }
            }

            // Obtener ID de GET o de los datos
            $id = $_GET['id'] ?? $data['IdEmpleado'] ?? null;
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID de empleado requerido']);
                return;
            }

            $empleado = $this->repository->read($id);
            if (!$empleado) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Empleado no encontrado']);
                return;
            }

            // Actualizar campos (usar datos del formulario o mantener los existentes)
            if (isset($data['NumEmpleado']) && !empty($data['NumEmpleado'])) $empleado->setNumEmpleado($data['NumEmpleado']);
            if (isset($data['Nombre']) && !empty($data['Nombre'])) $empleado->setNombre($data['Nombre']);
            if (isset($data['Apellido']) && !empty($data['Apellido'])) $empleado->setApellido($data['Apellido']);
            if (isset($data['Sexo']) && !empty($data['Sexo'])) $empleado->setSexo($data['Sexo']);

            // Manejar foto si se subió
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                // Eliminar foto anterior si existe
                if ($empleado->getFoto()) {
                    $oldPhoto = __DIR__ . '/../../' . $empleado->getFoto();
                    if (file_exists($oldPhoto)) {
                        unlink($oldPhoto);
                    }
                }
                $fotoPath = $this->handlePhotoUpload($_FILES['foto'], $empleado->getNumEmpleado());
                $empleado->setFoto($fotoPath);
            }

            // Manejar huella dactilar si se proporcionó (puede venir en POST o en data)
            $fingerprintData = $data['fingerprint_data'] ?? $_POST['fingerprint_data'] ?? null;
            if ($fingerprintData && !empty($fingerprintData)) {
                // Eliminar huella anterior si existe
                if ($empleado->getBiometrico()) {
                    $oldFp = __DIR__ . '/../../' . $empleado->getBiometrico();
                    if (file_exists($oldFp)) {
                        unlink($oldFp);
                    }
                }
                $fingerprintPath = $this->handleFingerprintSave($fingerprintData, $empleado->getNumEmpleado());
                $empleado->setBiometrico($fingerprintPath);
            }

            // Validar
            $errors = $empleado->validate();
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }

            $this->repository->update($empleado);
            echo json_encode(['success' => true, 'message' => 'Empleado actualizado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar empleado
     */
    private function delete($id) {
        header('Content-Type: application/json');
        try {
            $empleado = $this->repository->read($id);
            if (!$empleado) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Empleado no encontrado']);
                return;
            }

            // Eliminar archivos asociados
            if ($empleado->getFoto()) {
                $photoPath = __DIR__ . '/../../' . $empleado->getFoto();
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            if ($empleado->getBiometrico()) {
                $fpPath = __DIR__ . '/../../' . $empleado->getBiometrico();
                if (file_exists($fpPath)) {
                    unlink($fpPath);
                }
            }

            $this->repository->delete($id);
            echo json_encode(['success' => true, 'message' => 'Empleado eliminado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Manejar subida de foto
     */
    private function handlePhotoUpload($file, $numEmpleado) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido. Solo JPG y PNG');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'photo_' . $numEmpleado . '_' . time() . '.' . $extension;
        $filepath = $this->photosDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Error al guardar la foto');
        }

        return 'employees_photos/' . $filename;
    }

    /**
     * Manejar guardado de huella dactilar
     */
    private function handleFingerprintSave($fingerprintData, $numEmpleado) {
        // Decodificar base64
        $imageData = base64_decode($fingerprintData);
        if ($imageData === false) {
            throw new Exception('Datos de huella inválidos');
        }

        $filename = $numEmpleado . '.png';
        $filepath = $this->fingerprintDir . '/' . $filename;

        if (file_put_contents($filepath, $imageData) === false) {
            throw new Exception('Error al guardar la huella');
        }

        return 'employees_fingerprint/' . $filename;
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'EmpleadosController.php') {
    $controller = new EmpleadosController();
    $controller->handleRequest();
}
?>

