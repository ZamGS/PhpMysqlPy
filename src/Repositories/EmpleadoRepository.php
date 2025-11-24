<?php
require_once __DIR__ . '/../Models/Empleado.php';
require_once __DIR__ . '/../../connection/config.php';

/**
 * Repositorio de Empleados
 * Maneja todas las operaciones de base de datos relacionadas con empleados
 */
class EmpleadoRepository {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Crear un nuevo empleado
     */
    public function create(Empleado $empleado) {
        $sql = "INSERT INTO Empleados (NumEmpleado, Nombre, Apellido, Sexo, Foto, Biometrico) 
                VALUES (:NumEmpleado, :Nombre, :Apellido, :Sexo, :Foto, :Biometrico)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':NumEmpleado' => $empleado->getNumEmpleado(),
            ':Nombre' => $empleado->getNombre(),
            ':Apellido' => $empleado->getApellido(),
            ':Sexo' => $empleado->getSexo(),
            ':Foto' => $empleado->getFoto(),
            ':Biometrico' => $empleado->getBiometrico()
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Leer un empleado por ID
     */
    public function read($id) {
        $sql = "SELECT * FROM Empleados WHERE IdEmpleado = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        return $data ? new Empleado($data) : null;
    }

    /**
     * Leer todos los empleados
     */
    public function readAll() {
        $sql = "SELECT * FROM Empleados ORDER BY Nombre, Apellido";
        $stmt = $this->pdo->query($sql);
        $results = $stmt->fetchAll();

        $empleados = [];
        foreach ($results as $data) {
            $empleados[] = new Empleado($data);
        }

        return $empleados;
    }

    /**
     * Actualizar un empleado
     */
    public function update(Empleado $empleado) {
        $sql = "UPDATE Empleados 
                SET NumEmpleado = :NumEmpleado, 
                    Nombre = :Nombre, 
                    Apellido = :Apellido, 
                    Sexo = :Sexo, 
                    Foto = :Foto, 
                    Biometrico = :Biometrico 
                WHERE IdEmpleado = :IdEmpleado";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':IdEmpleado' => $empleado->getIdEmpleado(),
            ':NumEmpleado' => $empleado->getNumEmpleado(),
            ':Nombre' => $empleado->getNombre(),
            ':Apellido' => $empleado->getApellido(),
            ':Sexo' => $empleado->getSexo(),
            ':Foto' => $empleado->getFoto(),
            ':Biometrico' => $empleado->getBiometrico()
        ]);
    }

    /**
     * Eliminar un empleado
     */
    public function delete($id) {
        $sql = "DELETE FROM Empleados WHERE IdEmpleado = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Buscar empleado por número de empleado
     */
    public function findByNumEmpleado($numEmpleado) {
        $sql = "SELECT * FROM Empleados WHERE NumEmpleado = :NumEmpleado";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':NumEmpleado' => $numEmpleado]);
        $data = $stmt->fetch();

        return $data ? new Empleado($data) : null;
    }

    /**
     * Buscar empleado por archivo biométrico
     */
    public function findByBiometrico($biometrico) {
        $sql = "SELECT * FROM Empleados WHERE Biometrico = :Biometrico";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':Biometrico' => $biometrico]);
        $data = $stmt->fetch();

        return $data ? new Empleado($data) : null;
    }

    /**
     * Buscar empleados por nombre o número de empleado (para filtros)
     */
    public function search($searchTerm) {
        $sql = "SELECT * FROM Empleados 
                WHERE Nombre LIKE :search 
                   OR Apellido LIKE :search 
                   OR NumEmpleado LIKE :search 
                ORDER BY Nombre, Apellido";
        $stmt = $this->pdo->prepare($sql);
        $searchPattern = "%{$searchTerm}%";
        $stmt->execute([':search' => $searchPattern]);
        $results = $stmt->fetchAll();

        $empleados = [];
        foreach ($results as $data) {
            $empleados[] = new Empleado($data);
        }

        return $empleados;
    }
}
?>

