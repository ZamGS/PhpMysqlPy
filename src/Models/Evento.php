<?php
/**
 * Modelo Evento
 * Representa un evento de asistencia en el sistema
 */

class Evento {
    private $IdEvento;
    private $IdEmpleado;
    private $Hora;
    private $Fecha;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->IdEvento = $data['IdEvento'] ?? null;
            $this->IdEmpleado = $data['IdEmpleado'] ?? null;
            $this->Hora = $data['Hora'] ?? null;
            $this->Fecha = $data['Fecha'] ?? null;
        }
    }

    // Getters
    public function getIdEvento() {
        return $this->IdEvento;
    }

    public function getIdEmpleado() {
        return $this->IdEmpleado;
    }

    public function getHora() {
        return $this->Hora;
    }

    public function getFecha() {
        return $this->Fecha;
    }

    // Setters
    public function setIdEmpleado($idEmpleado) {
        $this->IdEmpleado = $idEmpleado;
    }

    public function setHora($hora) {
        $this->Hora = $hora;
    }

    public function setFecha($fecha) {
        $this->Fecha = $fecha;
    }

    // Validaciones
    public function validate() {
        $errors = [];

        if (empty($this->IdEmpleado)) {
            $errors[] = "El ID de empleado es requerido";
        }

        if (empty($this->Hora)) {
            $errors[] = "La hora es requerida";
        }

        if (empty($this->Fecha)) {
            $errors[] = "La fecha es requerida";
        }

        return $errors;
    }

    // Convertir a array
    public function toArray() {
        return [
            'IdEvento' => $this->IdEvento,
            'IdEmpleado' => $this->IdEmpleado,
            'Hora' => $this->Hora,
            'Fecha' => $this->Fecha
        ];
    }
}
?>

