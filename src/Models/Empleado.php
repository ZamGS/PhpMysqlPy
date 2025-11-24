<?php
/**
 * Modelo Empleado
 * Representa un empleado en el sistema
 */

class Empleado {
    private $IdEmpleado;
    private $NumEmpleado;
    private $Nombre;
    private $Apellido;
    private $Sexo;
    private $Foto;
    private $Biometrico;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->IdEmpleado = $data['IdEmpleado'] ?? null;
            $this->NumEmpleado = $data['NumEmpleado'] ?? '';
            $this->Nombre = $data['Nombre'] ?? '';
            $this->Apellido = $data['Apellido'] ?? '';
            $this->Sexo = $data['Sexo'] ?? '';
            $this->Foto = $data['Foto'] ?? null;
            $this->Biometrico = $data['Biometrico'] ?? null;
        }
    }

    // Getters
    public function getIdEmpleado() {
        return $this->IdEmpleado;
    }

    public function getNumEmpleado() {
        return $this->NumEmpleado;
    }

    public function getNombre() {
        return $this->Nombre;
    }

    public function getApellido() {
        return $this->Apellido;
    }

    public function getSexo() {
        return $this->Sexo;
    }

    public function getFoto() {
        return $this->Foto;
    }

    public function getBiometrico() {
        return $this->Biometrico;
    }

    // Setters
    public function setNumEmpleado($numEmpleado) {
        $this->NumEmpleado = $numEmpleado;
    }

    public function setNombre($nombre) {
        $this->Nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->Apellido = $apellido;
    }

    public function setSexo($sexo) {
        $this->Sexo = $sexo;
    }

    public function setFoto($foto) {
        $this->Foto = $foto;
    }

    public function setBiometrico($biometrico) {
        $this->Biometrico = $biometrico;
    }

    // Validaciones
    public function validate() {
        $errors = [];

        if (empty($this->NumEmpleado)) {
            $errors[] = "El número de empleado es requerido";
        } elseif (strlen($this->NumEmpleado) > 10) {
            $errors[] = "El número de empleado no puede exceder 10 caracteres";
        }

        if (empty($this->Nombre)) {
            $errors[] = "El nombre es requerido";
        } elseif (strlen($this->Nombre) > 50) {
            $errors[] = "El nombre no puede exceder 50 caracteres";
        }

        if (empty($this->Apellido)) {
            $errors[] = "El apellido es requerido";
        } elseif (strlen($this->Apellido) > 50) {
            $errors[] = "El apellido no puede exceder 50 caracteres";
        }

        if (empty($this->Sexo)) {
            $errors[] = "El sexo es requerido";
        } elseif (!in_array(strtoupper($this->Sexo), ['M', 'F'])) {
            $errors[] = "El sexo debe ser M o F";
        }

        return $errors;
    }

    // Convertir a array
    public function toArray() {
        return [
            'IdEmpleado' => $this->IdEmpleado,
            'NumEmpleado' => $this->NumEmpleado,
            'Nombre' => $this->Nombre,
            'Apellido' => $this->Apellido,
            'Sexo' => $this->Sexo,
            'Foto' => $this->Foto,
            'Biometrico' => $this->Biometrico
        ];
    }
}
?>

