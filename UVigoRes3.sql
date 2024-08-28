SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: `UVigoRes4`
DROP SCHEMA IF EXISTS `UVigoRes4`;

CREATE DATABASE IF NOT EXISTS `UVigoRes4` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `UVigoRes4`;

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

-- Creación de la tabla Centro
CREATE TABLE Centro (
    ID_Centro INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Direccion VARCHAR(100) NOT NULL,
    Telefono VARCHAR(15) NOT NULL,
    Email VARCHAR(100) NOT NULL
);

-- Creación de la tabla Recurso
CREATE TABLE Recurso (
    ID_Recurso INT AUTO_INCREMENT PRIMARY KEY,
    Tipo VARCHAR(50) NOT NULL,
    Descripcion TEXT NOT NULL,
    Disponibilidad ENUM('Disponible', 'No disponible') NOT NULL,
    ID_Centro INT NOT NULL,
    FOREIGN KEY (ID_Centro) REFERENCES Centro(ID_Centro)
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
    Asignada BOOLEAN NOT NULL DEFAULT FALSE,
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

-- Tabla para relacionar Centro con Conserje
CREATE TABLE Centro_Conserje (
    ID_Centro INT,
    ID_Usuario INT,
    PRIMARY KEY (ID_Centro, ID_Usuario),
    FOREIGN KEY (ID_Centro) REFERENCES Centro(ID_Centro),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

-- Trigger para asegurar que solo conserjes sean añadidos a Centro_Conserje
DELIMITER //

CREATE TRIGGER Centro_Conserje_Insert
BEFORE INSERT ON Centro_Conserje
FOR EACH ROW
BEGIN
    DECLARE v_Rol VARCHAR(50);
    SET v_Rol = (SELECT Rol FROM Usuario WHERE ID_Usuario = NEW.ID_Usuario);
    IF v_Rol != 'Personal de conserjeria' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Solo el personal de conserjería puede ser asignado a un centro como conserje.';
    END IF;
END //

DELIMITER ;

-- Tabla para relacionar Centro con Becario
CREATE TABLE Centro_Becario (
    ID_Centro INT,
    ID_Usuario INT,
    PRIMARY KEY (ID_Centro, ID_Usuario),
    FOREIGN KEY (ID_Centro) REFERENCES Centro(ID_Centro),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

-- Trigger para asegurar que solo becarios sean añadidos a Centro_Becario
DELIMITER //

CREATE TRIGGER Centro_Becario_Insert
BEFORE INSERT ON Centro_Becario
FOR EACH ROW
BEGIN
    DECLARE v_Rol VARCHAR(50);
    SET v_Rol = (SELECT Rol FROM Usuario WHERE ID_Usuario = NEW.ID_Usuario);
    IF v_Rol != 'Becario de infraestrucura' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Solo los becarios de infraestructura pueden ser asignados a un centro como becario.';
    END IF;
END //

DELIMITER ;

-- Creación de la tabla Incidencia_Asignacion
CREATE TABLE Incidencia_Asignacion (
    ID_Incidencia INT NOT NULL,
    ID_Usuario INT NOT NULL,
    Fecha_Asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID_Incidencia, ID_Usuario),
    FOREIGN KEY (ID_Incidencia) REFERENCES Incidencia(ID_Incidencia),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

-- Crear vista para obtener el centro asociado a cada usuario becario o conserje
CREATE VIEW Vista_Usuario_Centro AS
SELECT 
    u.ID_Usuario,
    u.Rol,
    COALESCE(cc.ID_Centro, cb.ID_Centro) AS Centro_ID
FROM 
    Usuario u
LEFT JOIN 
    Centro_Conserje cc ON u.ID_Usuario = cc.ID_Usuario
LEFT JOIN 
    Centro_Becario cb ON u.ID_Usuario = cb.ID_Usuario
WHERE 
    u.Rol IN ('Becario de infraestrucura', 'Personal de conserjeria');

-- Creación del Trigger para validar asignaciones directas
DELIMITER //

CREATE TRIGGER before_insert_incidencia_asignacion
BEFORE INSERT ON Incidencia_Asignacion
FOR EACH ROW
BEGIN
    DECLARE v_CentroUsuario INT;
    DECLARE v_CentroRecurso INT;

    -- Obtener el centro del usuario
    SET v_CentroUsuario = (SELECT Centro_ID FROM Vista_Usuario_Centro WHERE ID_Usuario = NEW.ID_Usuario);

    -- Obtener el centro del recurso asociado a la incidencia
    SET v_CentroRecurso = (SELECT ID_Centro FROM Recurso 
                           WHERE ID_Recurso = (SELECT ID_Recurso FROM Incidencia WHERE ID_Incidencia = NEW.ID_Incidencia));

    -- Verificar que el usuario y el recurso estén en el mismo centro
    IF v_CentroUsuario IS NULL OR v_CentroUsuario != v_CentroRecurso THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El usuario no pertenece al mismo centro que el recurso asociado a la incidencia.';
    END IF;
END //

DELIMITER ;

-- Procedimiento para asignar incidencias
DELIMITER //

CREATE PROCEDURE AsignarIncidencia(IN p_ID_Incidencia INT, IN p_ID_Usuario INT)
BEGIN
    DECLARE v_Rol VARCHAR(50);
    DECLARE v_CentroUsuario INT;
    DECLARE v_CentroRecurso INT;
    
    -- Verificar si la incidencia ya está asignada
    IF (SELECT Asignada FROM Incidencia WHERE ID_Incidencia = p_ID_Incidencia) = TRUE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La incidencia ya está asignada.';
    END IF;
    
    -- Obtener el rol y el centro del usuario
    SET v_Rol = (SELECT Rol FROM Usuario WHERE ID_Usuario = p_ID_Usuario);
    SET v_CentroUsuario = (SELECT Centro_ID FROM Vista_Usuario_Centro WHERE ID_Usuario = p_ID_Usuario);
    
    -- Verificar el rol del usuario
    IF v_Rol NOT IN ('Becario de infraestrucura', 'Personal de conserjeria') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Solo los becarios de infraestructura o personal de conserjería pueden ser asignados a incidencias.';
    END IF;
    
    -- Obtener el centro del recurso asociado a la incidencia
    SET v_CentroRecurso = (SELECT ID_Centro FROM Recurso 
                           WHERE ID_Recurso = (SELECT ID_Recurso FROM Incidencia WHERE ID_Incidencia = p_ID_Incidencia));
    
    -- Verificar que el usuario y el recurso estén en el mismo centro
    IF v_CentroUsuario IS NULL OR v_CentroUsuario != v_CentroRecurso THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: El usuario no pertenece al mismo centro que el recurso asociado a la incidencia.';
    END IF;
    
    -- Asignar la incidencia
    INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (p_ID_Incidencia, p_ID_Usuario);
    
    -- Marcar la incidencia como asignada
    UPDATE Incidencia SET Asignada = TRUE WHERE ID_Incidencia = p_ID_Incidencia;
    
END //

DELIMITER ;

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
('55443322E', 'Admin', 'Adminez', '1005', 'admin@uvigo.es', 'Admin', 'admin'),
('13579246F', 'Carlos', 'Rodríguez', '1006', 'carlos.rodriguez@uvigo.es', 'Estudiante', 'contrasena5'),
('24681357G', 'Laura', 'Sánchez', '1007', 'laura.sanchez@uvigo.es', 'Docente', 'contrasena6'),
('19283746H', 'David', 'Martínez', '1008', 'david.martinez@uvigo.es', 'Estudiante', 'contrasena7'),
('37482910I', 'Lucía', 'Ramírez', '1009', 'lucia.ramirez@uvigo.es', 'Docente', 'contrasena8'),
('38492056J', 'Sofía', 'Torres', '1010', 'sofia.torres@uvigo.es', 'Personal de conserjeria', 'contrasena9'),
('29384756K', 'Miguel', 'Santos', '1011', 'miguel.santos@uvigo.es', 'Becario de infraestrucura', 'contrasena10'),
('12938745L', 'Isabel', 'Vázquez', '1012', 'isabel.vazquez@uvigo.es', 'Docente', 'contrasena11');

-- Inserción de datos en la tabla Centro
INSERT INTO Centro (Nombre, Direccion, Telefono, Email) VALUES
('Facultad de Ciencias Económicas y Empresariales', 'Campus Lagoas-Marcosende, Vigo', '986812345', 'fceconomicas@uvigo.es'),
('Facultad de Derecho', 'Campus Lagoas-Marcosende, Vigo', '986812678', 'fderecho@uvigo.es'),
('Escuela de Ingeniería Industrial', 'Campus Lagoas-Marcosende, Vigo', '986812234', 'eingenieriaindustrial@uvigo.es'),
('Facultad de Biología', 'Campus Lagoas-Marcosende, Vigo', '986812890', 'fbiologia@uvigo.es'),
('Facultad de Ciencias del Mar', 'Campus Lagoas-Marcosende, Vigo', '986812456', 'fcienciasdelmar@uvigo.es'),
('Facultad de Filología y Traducción', 'Campus Lagoas-Marcosende, Vigo', '986812567', 'ffilologiatraduccion@uvigo.es'),
('Facultad de Ciencias Sociales y de la Comunicación', 'Campus A Xunqueira, Pontevedra', '986801234', 'fcienciassociales@uvigo.es'),
('Facultad de Ciencias de la Educación y del Deporte', 'Campus A Xunqueira, Pontevedra', '986801567', 'fcienciaseducacion@uvigo.es'),
('Escuela de Ingeniería de Telecomunicación', 'Campus Lagoas-Marcosende, Vigo', '986812678', 'eingenieriatelecomunicacion@uvigo.es'),
('Facultad de Historia', 'Campus As Lagoas, Ourense', '988387123', 'fhistoria@uvigo.es'),
('Facultad de Ciencias Jurídicas', 'Campus As Lagoas, Ourense', '988387456', 'fcienciasjuridicas@uvigo.es'),
('Escuela Superior de Ingenieros de Minas', 'Campus As Lagoas, Ourense', '988387789', 'esiminas@uvigo.es');

-- Inserción de datos en la tabla Recurso
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Aula', 'Aula de teoría 101', 'Disponible', 2),
('Laboratorio', 'Laboratorio de Informática 202', 'Disponible', 9),
('Sala', 'Sala de Juntas', 'Disponible', 3),
('Proyector', 'Proyector Epson EB-S41', 'Disponible', 4),
('Impresora', 'Impresora HP LaserJet Pro MFP M428fdw', 'No disponible', 5),
('Microscopio', 'Microscopio electrónico', 'Disponible', 6),
('Cámara', 'Cámara Canon EOS 4000D', 'Disponible', 7),
('Aula', 'Aula de informática 202', 'Disponible', 9),
('Portátil', 'Portátil HP Envy 13', 'Disponible', 10),
('Tablet', 'Tablet Samsung Galaxy Tab S6', 'No disponible', 11),
('Proyector', 'Proyector Sony VPL-DX221', 'Disponible', 12),
('Aula', 'Aula de teoría 204', 'Disponible', 3),
('Laboratorio', 'Laboratorio de Química 303', 'Disponible', 4);

-- Inserción de datos en la tabla Reserva
INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Estado) VALUES
(1, 1, '2024-07-20 10:00:00', 2, 'Confirmada'),
(2, 2, '2024-07-21 14:00:00', 4, 'Confirmada'),
(3, 3, '2024-07-22 08:00:00', 1, 'No Confirmada'),
(4, 4, '2024-07-23 09:00:00', 1, 'Confirmada'),
(5, 5, '2024-07-24 11:00:00', 3, 'Confirmada'),
(6, 6, '2024-07-25 13:00:00', 5, 'No Confirmada'),
(7, 7, '2024-07-26 15:00:00', 6, 'Confirmada'),
(8, 8, '2024-07-27 10:00:00', 2, 'No Confirmada'),
(9, 9, '2024-07-28 12:00:00', 4, 'Confirmada'),
(10, 10, '2024-07-29 11:00:00', 3, 'No Confirmada'),
(11, 11, '2024-07-30 09:00:00', 1, 'Confirmada'),
(12, 12, '2024-08-01 14:00:00', 5, 'Confirmada'),
(1, 13, '2024-08-02 08:00:00', 1, 'Confirmada'),
(2, 14, '2024-08-03 10:00:00', 2, 'No Confirmada');

-- Inserción de datos en la tabla Incidencia
INSERT INTO Incidencia (ID_Usuario, ID_Recurso, Descripcion_Problema, Fecha_Reporte, Estado) VALUES
(1, 1, 'El portátil no enciende', '2024-07-20 10:15:00', 'Pendiente'),
(2, 2, 'Proyector no funciona', '2024-07-21 14:30:00', 'Resuelta'),
(3, 3, 'Aula sin aire acondicionado', '2024-07-22 08:45:00', 'Pendiente'),
(4, 4, 'Laboratorio sin internet', '2024-07-23 09:20:00', 'Resuelta'),
(5, 5, 'Sala de juntas sin luz', '2024-07-24 11:50:00', 'Pendiente'),
(6, 6, 'Impresora sin papel', '2024-07-25 13:30:00', 'Resuelta'),
(7, 7, 'Microscopio no funciona', '2024-07-26 15:20:00', 'Pendiente'),
(8, 8, 'Cámara sin batería', '2024-07-27 10:45:00', 'Resuelta'),
(9, 9, 'Aula con equipos defectuosos', '2024-07-28 12:30:00', 'Pendiente'),
(10, 10, 'Portátil con pantalla rota', '2024-07-29 11:30:00', 'Resuelta'),
(11, 11, 'Tablet no enciende', '2024-07-30 09:20:00', 'Pendiente'),
(12, 12, 'Proyector sin señal', '2024-08-01 14:15:00', 'Resuelta'),
(1, 13, 'Aula con ventilación defectuosa', '2024-08-02 08:20:00', 'Pendiente'),
(2, 14, 'Laboratorio sin acceso a red', '2024-08-03 10:30:00', 'Resuelta');

-- Inserción de datos en la tabla Penalizacion
INSERT INTO Penalizacion (ID_Usuario, Fecha_Inicio_Penalizacion, Fecha_Fin_Penalizacion) VALUES
(1, '2024-07-20', '2024-07-27'),
(2, '2024-07-21', '2024-07-28'),
(3, '2024-07-22', '2024-07-29'),
(4, '2024-07-23', '2024-07-30'),
(5, '2024-07-24', '2024-07-31'),
(6, '2024-07-25', '2024-08-01'),
(7, '2024-07-26', '2024-08-02'),
(8, '2024-07-27', '2024-08-03'),
(9, '2024-07-28', '2024-08-04'),
(10, '2024-07-29', '2024-08-05'),
(11, '2024-07-30', '2024-08-06'),
(12, '2024-08-01', '2024-08-08');

-- Inserción de datos en la tabla Centro_Becario
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(1, 3),  -- Asignar al usuario Ana López (ID_Usuario 3) como becaria en el centro 1
(4, 11); -- Asignar al usuario Miguel Santos (ID_Usuario 11) como becario en el centro 4

-- Inserción de datos en la tabla Centro_Conserje
INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(2, 4),  -- Asignar al usuario Luis Fernández (ID_Usuario 4) como personal de conserjería en el centro 2
(3, 10); -- Asignar al usuario Sofía Torres (ID_Usuario 10) como personal de conserjería en el centro 3


-- COMMIT;
COMMIT;
