<?php
require_once __DIR__ . '/../Models/Evento.php';
require_once __DIR__ . '/../../connection/config.php';

/**
 * Repositorio de Eventos
 * Maneja todas las operaciones de base de datos relacionadas con eventos
 */
class EventoRepository {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Crear un nuevo evento
     */
    public function create(Evento $evento) {
        $sql = "INSERT INTO Eventos (IdEmpleado, Hora, Fecha) 
                VALUES (:IdEmpleado, :Hora, :Fecha)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':IdEmpleado' => $evento->getIdEmpleado(),
            ':Hora' => $evento->getHora(),
            ':Fecha' => $evento->getFecha()
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Leer un evento por ID
     */
    public function read($id) {
        $sql = "SELECT * FROM Eventos WHERE IdEvento = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        return $data ? new Evento($data) : null;
    }

    /**
     * Leer todos los eventos
     */
    public function readAll() {
        $sql = "SELECT * FROM Eventos ORDER BY Fecha DESC, Hora DESC";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll();

        $eventos = [];
        foreach ($results as $data) {
            $eventos[] = new Evento($data);
        }

        return $eventos;
    }

    /**
     * Eliminar un evento
     */
    public function delete($id) {
        $sql = "DELETE FROM Eventos WHERE IdEvento = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtener los últimos 10 eventos con información de empleados
     */
    public function getLast10() {
        $sql = "SELECT e.IdEvento, e.Hora, e.Fecha, 
                       emp.IdEmpleado, emp.NumEmpleado, emp.Nombre, emp.Apellido
                FROM Eventos e
                INNER JOIN Empleados emp ON e.IdEmpleado = emp.IdEmpleado
                ORDER BY e.Fecha DESC, e.Hora DESC
                LIMIT 10";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas semanales (Lunes a Sábado de la semana actual)
     */
    public function getWeeklyStats() {
        // Calcular lunes de la semana actual
        $monday = date('Y-m-d', strtotime('monday this week'));
        $saturday = date('Y-m-d', strtotime('saturday this week'));

        $sql = "SELECT DATE(e.Fecha) as Fecha, COUNT(DISTINCT e.IdEmpleado) as TotalUsuarios
                FROM Eventos e
                WHERE e.Fecha BETWEEN :monday AND :saturday
                GROUP BY DATE(e.Fecha)
                ORDER BY e.Fecha ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':monday' => $monday,
            ':saturday' => $saturday
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Crear evento desde checador (usa hora y fecha actuales)
     */
    public function createFromChecador($idEmpleado) {
        $evento = new Evento([
            'IdEmpleado' => $idEmpleado,
            'Hora' => date('H:i:s'),
            'Fecha' => date('Y-m-d')
        ]);

        return $this->create($evento);
    }
}
?>

