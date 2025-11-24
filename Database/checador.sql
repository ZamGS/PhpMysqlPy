-- Script de creación de base de datos para Sistema Checador
-- Base de datos: Checador

CREATE DATABASE IF NOT EXISTS Checador;
USE Checador;

-- Tabla Empleados
CREATE TABLE IF NOT EXISTS Empleados (
    IdEmpleado INT AUTO_INCREMENT PRIMARY KEY,
    NumEmpleado VARCHAR(10) NOT NULL,
    Nombre VARCHAR(50) NOT NULL,
    Apellido VARCHAR(50) NOT NULL,
    Sexo VARCHAR(1) NOT NULL,
    Foto VARCHAR(50) NULL,
    Biometrico VARCHAR(50) NULL,
    UNIQUE KEY unique_num_empleado (NumEmpleado),
    INDEX idx_num_empleado (NumEmpleado),
    INDEX idx_biometrico (Biometrico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla Eventos
CREATE TABLE IF NOT EXISTS Eventos (
    IdEvento INT AUTO_INCREMENT PRIMARY KEY,
    IdEmpleado INT NOT NULL,
    Hora TIME NOT NULL,
    Fecha DATE NOT NULL,
    FOREIGN KEY (IdEmpleado) REFERENCES Empleados(IdEmpleado) ON DELETE CASCADE,
    INDEX idx_fecha (Fecha),
    INDEX idx_id_empleado (IdEmpleado),
    INDEX idx_fecha_hora (Fecha, Hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

