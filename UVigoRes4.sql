SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de datos: `UVigoRes5`
DROP SCHEMA IF EXISTS `UVigoRes5`;

CREATE DATABASE IF NOT EXISTS `UVigoRes5` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `UVigoRes5`;

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
    IF v_Rol NOT IN ('Becario de infraestrucura', 'Personal de conserjería') THEN
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
-- Inserción de usuarios Becarios de infraestructura
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('11111111A', 'Juan', 'Pérez', 'NIU001', 'juan.perez@uvigo.es', 'Becario de infraestrucura', 'contrasena1'),
('22222222B', 'Ana', 'García', 'NIU002', 'ana.garcia@uvigo.es', 'Becario de infraestrucura', 'contrasena2'),
('33333333C', 'Carlos', 'López', 'NIU003', 'carlos.lopez@uvigo.es', 'Becario de infraestrucura', 'contrasena3'),
('44444444D', 'Laura', 'Martín', 'NIU004', 'laura.martin@uvigo.es', 'Becario de infraestrucura', 'contrasena4'),
('55555555E', 'David', 'Hernández', 'NIU005', 'david.hernandez@uvigo.es', 'Becario de infraestrucura', 'contrasena5'),
('66666666F', 'Marta', 'Díaz', 'NIU006', 'marta.diaz@uvigo.es', 'Becario de infraestrucura', 'contrasena6'),
('77777777G', 'José', 'Santos', 'NIU007', 'jose.santos@uvigo.es', 'Becario de infraestrucura', 'contrasena7'),
('88888888H', 'Lucía', 'Ramírez', 'NIU008', 'lucia.ramirez@uvigo.es', 'Becario de infraestrucura', 'contrasena8'),
('99999999I', 'Fernando', 'Gómez', 'NIU009', 'fernando.gomez@uvigo.es', 'Becario de infraestrucura', 'contrasena9'),
('12345678J', 'Patricia', 'Muñoz', 'NIU010', 'patricia.munoz@uvigo.es', 'Becario de infraestrucura', 'contrasena10'),
('23456789K', 'Sergio', 'Vega', 'NIU011', 'sergio.vega@uvigo.es', 'Becario de infraestrucura', 'contrasena11'),
('34567890L', 'Beatriz', 'Molina', 'NIU012', 'beatriz.molina@uvigo.es', 'Becario de infraestrucura', 'contrasena12'),
('45678901M', 'Alberto', 'Ortiz', 'NIU013', 'alberto.ortiz@uvigo.es', 'Becario de infraestrucura', 'contrasena13'),
('56789012N', 'Elena', 'Torres', 'NIU014', 'elena.torres@uvigo.es', 'Becario de infraestrucura', 'contrasena14'),
('67890123O', 'Manuel', 'Vázquez', 'NIU015', 'manuel.vazquez@uvigo.es', 'Becario de infraestrucura', 'contrasena15'),
('78901234P', 'Cristina', 'Márquez', 'NIU016', 'cristina.marquez@uvigo.es', 'Becario de infraestrucura', 'contrasena16'),
('89012345Q', 'Rafael', 'Navarro', 'NIU017', 'rafael.navarro@uvigo.es', 'Becario de infraestrucura', 'contrasena17'),
('90123456R', 'Isabel', 'Ríos', 'NIU018', 'isabel.rios@uvigo.es', 'Becario de infraestrucura', 'contrasena18'),
('01234567S', 'Pablo', 'Domínguez', 'NIU019', 'pablo.dominguez@uvigo.es', 'Becario de infraestrucura', 'contrasena19'),
('12345678T', 'Sofía', 'Ibáñez', 'NIU020', 'sofia.ibanez@uvigo.es', 'Becario de infraestrucura', 'contrasena20'),
('23456789U', 'Miguel', 'Guerrero', 'NIU021', 'miguel.guerrero@uvigo.es', 'Becario de infraestrucura', 'contrasena21'),
('34567890V', 'Raquel', 'Fuentes', 'NIU022', 'raquel.fuentes@uvigo.es', 'Becario de infraestrucura', 'contrasena22'),
('45678901W', 'Hugo', 'Campos', 'NIU023', 'hugo.campos@uvigo.es', 'Becario de infraestrucura', 'contrasena23'),
('56789012X', 'Claudia', 'Vargas', 'NIU024', 'claudia.vargas@uvigo.es', 'Becario de infraestrucura', 'contrasena24'),
('67890123Y', 'Alejandro', 'Cruz', 'NIU025', 'alejandro.cruz@uvigo.es', 'Becario de infraestrucura', 'contrasena25'),
('78901234Z', 'Natalia', 'Pascual', 'NIU026', 'natalia.pascual@uvigo.es', 'Becario de infraestrucura', 'contrasena26'),
('89012345A', 'Andrés', 'Reyes', 'NIU027', 'andres.reyes@uvigo.es', 'Becario de infraestrucura', 'contrasena27'),
('90123456B', 'Eva', 'Lorenzo', 'NIU028', 'eva.lorenzo@uvigo.es', 'Becario de infraestrucura', 'contrasena28'),
('01234567C', 'Daniel', 'Blanco', 'NIU029', 'daniel.blanco@uvigo.es', 'Becario de infraestrucura', 'contrasena29'),
('12345678D', 'Victoria', 'Morales', 'NIU030', 'victoria.morales@uvigo.es', 'Becario de infraestrucura', 'contrasena30'),
('23456789E', 'Álvaro', 'Crespo', 'NIU031', 'alvaro.crespo@uvigo.es', 'Becario de infraestrucura', 'contrasena31'),
('34567890F', 'Laura', 'Álvarez', 'NIU032', 'laura.alvarez@uvigo.es', 'Becario de infraestrucura', 'contrasena32'),
('45678901G', 'Mario', 'Castillo', 'NIU033', 'mario.castillo@uvigo.es', 'Becario de infraestrucura', 'contrasena33'),
('56789012H', 'Paula', 'Ferrer', 'NIU034', 'paula.ferrer@uvigo.es', 'Becario de infraestrucura', 'contrasena34'),
('67890123I', 'Javier', 'Pardo', 'NIU035', 'javier.pardo@uvigo.es', 'Becario de infraestrucura', 'contrasena35'),
('78901234J', 'Alicia', 'Santana', 'NIU036', 'alicia.santana@uvigo.es', 'Becario de infraestrucura', 'contrasena36');

-- Inserción de usuarios Personal de conserjería
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('11111112A', 'Tomás', 'Ruiz', 'NIU037', 'tomas.ruiz@uvigo.es', 'Personal de conserjeria', 'contrasena37'),
('22222223B', 'Rosa', 'Soler', 'NIU038', 'rosa.soler@uvigo.es', 'Personal de conserjeria', 'contrasena38'),
('33333334C', 'Ignacio', 'Serra', 'NIU039', 'ignacio.serra@uvigo.es', 'Personal de conserjeria', 'contrasena39'),
('44444445D', 'Mónica', 'Prado', 'NIU040', 'monica.prado@uvigo.es', 'Personal de conserjeria', 'contrasena40'),
('55555556E', 'Héctor', 'Sáez', 'NIU041', 'hector.saez@uvigo.es', 'Personal de conserjeria', 'contrasena41'),
('66666667F', 'Amelia', 'Cano', 'NIU042', 'amelia.cano@uvigo.es', 'Personal de conserjeria', 'contrasena42'),
('77777778G', 'Gonzalo', 'Marcos', 'NIU043', 'gonzalo.marcos@uvigo.es', 'Personal de conserjeria', 'contrasena43'),
('88888889H', 'Clara', 'Mata', 'NIU044', 'clara.mata@uvigo.es', 'Personal de conserjeria', 'contrasena44'),
('99999990I', 'Luis', 'Lázaro', 'NIU045', 'luis.lazaro@uvigo.es', 'Personal de conserjeria', 'contrasena45'),
('12345671J', 'Lorena', 'Barco', 'NIU046', 'lorena.barco@uvigo.es', 'Personal de conserjeria', 'contrasena46'),
('23456782K', 'Carlos', 'Bello', 'NIU047', 'carlos.bello@uvigo.es', 'Personal de conserjeria', 'contrasena47'),
('34567893L', 'Raúl', 'Caballero', 'NIU048', 'raul.caballero@uvigo.es', 'Personal de conserjeria', 'contrasena48'),
('45678904M', 'Ángela', 'Lago', 'NIU049', 'angela.lago@uvigo.es', 'Personal de conserjeria', 'contrasena49'),
('56789015N', 'Jorge', 'Pérez', 'NIU050', 'jorge.perez2@uvigo.es', 'Personal de conserjeria', 'contrasena50'),
('67890126O', 'Esther', 'Martín', 'NIU051', 'esther.martin@uvigo.es', 'Personal de conserjeria', 'contrasena51'),
('78901237P', 'Félix', 'Nieto', 'NIU052', 'felix.nieto@uvigo.es', 'Personal de conserjeria', 'contrasena52'),
('89012348Q', 'Silvia', 'Duarte', 'NIU053', 'silvia.duarte@uvigo.es', 'Personal de conserjeria', 'contrasena53'),
('90123459R', 'Víctor', 'Gálvez', 'NIU054', 'victor.galvez@uvigo.es', 'Personal de conserjeria', 'contrasena54'),
('01234560S', 'Ainhoa', 'Escribano', 'NIU055', 'ainhoa.escribano@uvigo.es', 'Personal de conserjeria', 'contrasena55'),
('12345671T', 'Roberto', 'Calero', 'NIU056', 'roberto.calero@uvigo.es', 'Personal de conserjeria', 'contrasena56'),
('23456782U', 'Cristina', 'Salinas', 'NIU057', 'cristina.salinas@uvigo.es', 'Personal de conserjeria', 'contrasena57'),
('34567893V', 'Emilio', 'Medina', 'NIU058', 'emilio.medina@uvigo.es', 'Personal de conserjeria', 'contrasena58'),
('45678904W', 'Lourdes', 'Caro', 'NIU059', 'lourdes.caro@uvigo.es', 'Personal de conserjeria', 'contrasena59'),
('56789015X', 'Alfredo', 'Valle', 'NIU060', 'alfredo.valle@uvigo.es', 'Personal de conserjeria', 'contrasena60');

-- Inserción de usuarios de otros roles
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('12345678A', 'Juan', 'Pérez', '1001', 'juan.perez@uvigo.es', 'Estudiante', '1'),
('docente', 'María', 'García', '1002', 'maria.garcia@uvigo.es', 'Docente', '2'),
('13579246F', 'Carlos', 'Rodríguez', '1006', 'carlos.rodriguez@uvigo.es', 'Estudiante', 'contrasena5'),
('24681357G', 'Laura', 'Sánchez', '1007', 'laura.sanchez@uvigo.es', 'Docente', 'contrasena6'),
('19283746H', 'David', 'Martínez', '1008', 'david.martinez@uvigo.es', 'Estudiante', 'contrasena7'),
('37482910I', 'Lucía', 'Ramírez', '1009', 'lucia.ramirez@uvigo.es', 'Docente', 'contrasena8'),
('admin', 'Admin', 'Adminez', '1005', 'admin@uvigo.es', 'Admin', 'admin');

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
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Portátil', 'Portátil Dell Latitude 7490', 'Disponible', 1),
('Aula', 'Aula de teoría 101', 'Disponible', 1),
('Aula', 'Aula de teoría 102', 'Disponible', 1),
('Aula', 'Aula de teoría 103', 'Disponible', 1),
('Aula', 'Aula de teoría 104', 'Disponible', 1),
('Aula', 'Aula de teoría 105', 'Disponible', 1),
('Aula', 'Aula de teoría 106', 'Disponible', 1),
('Aula', 'Aula de teoría 107', 'Disponible', 1),
('Aula', 'Aula de teoría 108', 'Disponible', 1),
('Aula', 'Aula de teoría 109', 'Disponible', 1),
('Aula', 'Aula de teoría 110', 'Disponible', 1),
('Sala', 'Sala de Juntas 1', 'Disponible', 1),
('Sala', 'Sala de Juntas 2', 'Disponible', 1),
('Microscopio', 'Microscopio óptico', 'Disponible', 4),
('Laboratorio', 'Laboratorio de Física', 'Disponible', 4),
('Laboratorio', 'Laboratorio de Química', 'Disponible', 3);

-- Inserción de datos en la tabla Reserva (corregidas)
INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Estado) VALUES
(1, 1, '2024-07-20 08:30:00', 1, 'Confirmada'),  -- Juan Pérez reserva portátil en la franja 1
(2, 2, '2024-07-21 10:30:00', 2, 'Confirmada'),  -- Ana García reserva portátil en la franja 2
(3, 3, '2024-07-22 08:30:00', 1, 'No Confirmada'),  -- Carlos López reserva portátil en la franja 1
(4, 4, '2024-07-23 10:30:00', 2, 'Confirmada'),  -- Laura Martín reserva portátil en la franja 2
(5, 5, '2024-07-24 12:30:00', 3, 'Confirmada'),  -- David Hernández reserva portátil en la franja 3
(6, 6, '2024-07-25 14:30:00', 4, 'No Confirmada'),  -- Marta Díaz reserva portátil en la franja 4
(7, 7, '2024-07-26 16:30:00', 5, 'Confirmada'),  -- José Santos reserva portátil en la franja 5
(8, 8, '2024-07-27 18:30:00', 6, 'No Confirmada'),  -- Lucía Ramírez reserva portátil en la franja 6
(9, 9, '2024-07-28 08:30:00', 1, 'Confirmada'),  -- Fernando Gómez reserva portátil en la franja 1
(10, 10, '2024-07-29 10:30:00', 2, 'No Confirmada'),  -- Patricia Muñoz reserva portátil en la franja 2
(11, 11, '2024-07-30 12:30:00', 3, 'Confirmada'),  -- Sergio Vega reserva portátil en la franja 3
(12, 12, '2024-08-01 14:30:00', 4, 'Confirmada'),  -- Beatriz Molina reserva portátil en la franja 4
(13, 13, '2024-08-02 08:30:00', 1, 'Confirmada'),  -- Alberto Ortiz reserva portátil en la franja 1
(14, 14, '2024-08-03 10:30:00', 2, 'No Confirmada');  -- Elena Torres reserva portátil en la franja 2

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

-- Asignación de incidencias según los centros
-- Incidencia 1 asignada al usuario 3 (Becario en el centro 1)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (1, 3);
-- Incidencia 3 asignada al usuario 11 (Becario en el centro 9)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (3, 11);
-- Incidencia 5 asignada al usuario 10 (Conserje en el centro 3)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (5, 10);
-- Incidencia 9 asignada al usuario 11 (Becario en el centro 9)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (9, 11);
-- Incidencia 13 asignada al usuario 10 (Conserje en el centro 3)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (13, 10);
-- Incidencia 14 asignada al usuario 11 (Becario en el centro 4)
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES (14, 11);

-- Marcar las incidencias asignadas como asignadas
UPDATE Incidencia SET Asignada = TRUE WHERE ID_Incidencia IN (1, 3, 5, 9, 13, 14);

-- COMMIT;
COMMIT;
