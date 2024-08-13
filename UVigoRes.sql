SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: `UVigoRes`
DROP SCHEMA IF EXISTS `UVigoRes`;

CREATE DATABASE IF NOT EXISTS `UVigoRes` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `UVigoRes`;

-- Creación de la tabla Usuario
CREATE TABLE Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    DNI VARCHAR(9) NOT NULL UNIQUE,
    Nombre VARCHAR(50) NOT NULL,
    Apellidos VARCHAR(50) NOT NULL,
    NIU VARCHAR(50) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL,
    Rol ENUM('Estudiante', 'Docente', 'Becario de infraestrucura', 'Personal de conserjeria', 'Admin') NOT NULL,
    Contrasena VARCHAR(100) NOT NULL
);

-- Creación de la tabla Recurso
CREATE TABLE Recurso (
    ID_Recurso INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Descripcion TEXT NOT NULL,
    Disponibilidad ENUM('Disponible', 'No disponible') NOT NULL
);

-- Creación de la tabla Franja
CREATE TABLE Franja (
    ID_Franja INT AUTO_INCREMENT PRIMARY KEY,
    Hora_Inicio TIME NOT NULL,
    Hora_Fin TIME NOT NULL
);

-- Creación de la tabla Reserva
CREATE TABLE Reserva (
    ID_Reserva INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT NOT NULL,
    ID_Recurso INT NOT NULL,
    Fecha_Hora_Reserva DATETIME NOT NULL,
    ID_Franja INT NOT NULL,
    Codigo_QR VARCHAR(100) NOT NULL,
    Estado ENUM('Confirmada', 'No Confirmada') NOT NULL,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Recurso) REFERENCES Recurso(ID_Recurso),
    FOREIGN KEY (ID_Franja) REFERENCES Franja(ID_Franja)
);

-- Creación de la tabla Incidencia
CREATE TABLE Incidencia (
    ID_Incidencia INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT NOT NULL,
    ID_Recurso INT NOT NULL,
    Descripcion_Problema TEXT NOT NULL,
    Fecha_Reporte DATETIME NOT NULL,
    Estado ENUM('Pendiente', 'Resuelta') NOT NULL,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Recurso) REFERENCES Recurso(ID_Recurso)
);

-- Creación de la tabla Penalizacion
CREATE TABLE Penalizacion (
    ID_Penalizacion INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT NOT NULL,
    Fecha_Inicio_Penalizacion DATE NOT NULL,
    Fecha_Fin_Penalizacion DATE NOT NULL,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

-- Inserción de datos en la tabla Franja
INSERT INTO Franja (Hora_Inicio, Hora_Fin) VALUES
('08:30:00', '10:30:00'),
('10:30:00', '12:30:00'),
('12:30:00', '14:30:00'),
('14:30:00', '16:30:00'),
('16:30:00', '18:30:00'),
('18:30:00', '20:30:00');

-- Inserción de datos en la tabla Usuario
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('12345678A', 'Juan', 'Pérez', '1001', 'juan.perez@uvigo.es', 'Estudiante', 'contrasena1'),
('87654321B', 'María', 'García', '1002', 'maria.garcia@uvigo.es', 'Docente', 'contrasena2'),
('11223344C', 'Ana', 'López', '1003', 'ana.lopez@uvigo.es', 'Becario de infraestrucura', 'contrasena3'),
('99887766D', 'Luis', 'Fernández', '1004', 'luis.fernandez@uvigo.es', 'Personal de conserjeria', 'contrasena4'),
('55443322E', 'Carlos', 'Sánchez', '1005', 'carlos.sanchez@uvigo.es', 'Admin', 'contrasena5');

-- Inserción de datos en la tabla Recurso
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad) VALUES
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible'),
('Aula', 'Aula de teoría 101', 'Disponible'),
('Laboratorio', 'Laboratorio de Informática 202', 'Disponible'),
('Sala', 'Sala de Juntas', 'Disponible'),
('Proyector', 'Proyector Epson EB-S41', 'Disponible');

-- Inserción de datos en la tabla Reserva
INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Codigo_QR, Estado) VALUES
(1, 1, '2024-07-20 10:00:00', 2, 'QR1234567890', 'Confirmada'),
(2, 2, '2024-07-21 14:00:00', 4, 'QR0987654321', 'Confirmada'),
(3, 3, '2024-07-22 08:00:00', 1, 'QR1122334455', 'No Confirmada'),
(4, 4, '2024-07-23 09:00:00', 1, 'QR5566778899', 'Confirmada'),
(5, 5, '2024-07-24 11:00:00', 3, 'QR6677889900', 'Confirmada');

-- Inserción de datos en la tabla Incidencia
INSERT INTO Incidencia (ID_Usuario, ID_Recurso, Descripcion_Problema, Fecha_Reporte, Estado) VALUES
(1, 1, 'El portátil no enciende', '2024-07-20 10:15:00', 'Pendiente'),
(2, 2, 'Proyector no funciona', '2024-07-21 14:30:00', 'Resuelta'),
(3, 3, 'Aula sin aire acondicionado', '2024-07-22 08:45:00', 'Pendiente'),
(4, 4, 'Laboratorio sin internet', '2024-07-23 09:20:00', 'Resuelta'),
(5, 5, 'Sala de juntas sin luz', '2024-07-24 11:50:00', 'Pendiente');

-- Inserción de datos en la tabla Penalizacion
INSERT INTO Penalizacion (ID_Usuario, Fecha_Inicio_Penalizacion, Fecha_Fin_Penalizacion) VALUES
(1, '2024-07-20', '2024-07-27'),
(2, '2024-07-21', '2024-07-28'),
(3, '2024-07-22', '2024-07-29'),
(4, '2024-07-23', '2024-07-30'),
(5, '2024-07-24', '2024-07-31');

COMMIT;
