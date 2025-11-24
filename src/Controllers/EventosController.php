<?php
require_once __DIR__ . '/../Repositories/EventoRepository.php';
require_once __DIR__ . '/../Models/Evento.php';

/**
 * Controlador de Eventos
 * Maneja las peticiones HTTP relacionadas con eventos
 */
class EventosController {
    private $repository;

    public function __construct() {
        $this->repository = new EventoRepository();
    }

    /**
     * Manejar peticiones
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        switch ($method) {
            case 'GET':
                if ($action === 'last10') {
                    $this->getLast10();
                } elseif ($action === 'weekly') {
                    $this->getWeeklyStats();
                } elseif (isset($_GET['id'])) {
                    $this->getById($_GET['id']);
                } else {
                    $this->getAll();
                }
                break;
            case 'POST':
                if ($action === 'checador') {
                    $this->createFromChecador();
                } else {
                    $this->create();
                }
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
     * Obtener todos los eventos
     */
    private function getAll() {
        header('Content-Type: application/json');
        try {
            $eventos = $this->repository->readAll();
            $data = array_map(function($e) {
                return $e->toArray();
            }, $eventos);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener evento por ID
     */
    private function getById($id) {
        header('Content-Type: application/json');
        try {
            $evento = $this->repository->read($id);
            if ($evento) {
                echo json_encode(['success' => true, 'data' => $evento->toArray()]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Evento no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener últimos 10 eventos con información de empleados
     */
    private function getLast10() {
        header('Content-Type: application/json');
        try {
            $eventos = $this->repository->getLast10();
            echo json_encode(['success' => true, 'data' => $eventos]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener estadísticas semanales
     */
    private function getWeeklyStats() {
        header('Content-Type: application/json');
        try {
            $stats = $this->repository->getWeeklyStats();
            echo json_encode(['success' => true, 'data' => $stats]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Crear nuevo evento
     */
    private function create() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                $data = $_POST;
            }

            $evento = new Evento($data);
            
            // Validar
            $errors = $evento->validate();
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }

            $id = $this->repository->create($evento);
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Evento creado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Crear evento desde checador (usa hora y fecha actuales)
     */
    private function createFromChecador() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                $data = $_POST;
            }

            if (!isset($data['IdEmpleado'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID de empleado requerido']);
                return;
            }

            $id = $this->repository->createFromChecador($data['IdEmpleado']);
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Evento registrado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar evento
     */
    private function delete($id) {
        header('Content-Type: application/json');
        try {
            $this->repository->delete($id);
            echo json_encode(['success' => true, 'message' => 'Evento eliminado exitosamente']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

// Ejecutar controlador si se accede directamente
if (basename($_SERVER['PHP_SELF']) === 'EventosController.php') {
    $controller = new EventosController();
    $controller->handleRequest();
}
?>

