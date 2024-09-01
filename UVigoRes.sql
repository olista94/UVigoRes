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
    Rol ENUM('Estudiante', 'Docente', 'Becario de infraestructura', 'Personal de conserjeria', 'Admin') NOT NULL,
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
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Recurso) REFERENCES Recurso(ID_Recurso) ON DELETE CASCADE,
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
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE,
    FOREIGN KEY (ID_Recurso) REFERENCES Recurso(ID_Recurso) ON DELETE CASCADE
);

-- Tabla para relacionar Centro con Conserje
CREATE TABLE Centro_Conserje (
    ID_Centro INT,
    ID_Usuario INT,
    PRIMARY KEY (ID_Centro, ID_Usuario),
    FOREIGN KEY (ID_Centro) REFERENCES Centro(ID_Centro) ON DELETE CASCADE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE 
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
    FOREIGN KEY (ID_Centro) REFERENCES Centro(ID_Centro) ON DELETE CASCADE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE 
);

-- Trigger para asegurar que solo becarios sean añadidos a Centro_Becario
DELIMITER //

CREATE TRIGGER Centro_Becario_Insert
BEFORE INSERT ON Centro_Becario
FOR EACH ROW
BEGIN
    DECLARE v_Rol VARCHAR(50);
    SET v_Rol = (SELECT Rol FROM Usuario WHERE ID_Usuario = NEW.ID_Usuario);
    IF v_Rol != 'Becario de infraestructura' THEN
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
    FOREIGN KEY (ID_Incidencia) REFERENCES Incidencia(ID_Incidencia) ON DELETE CASCADE,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario) ON DELETE CASCADE 
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
    u.Rol IN ('Becario de infraestructura', 'Personal de conserjeria');

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
    IF v_Rol NOT IN ('Becario de infraestructura', 'Personal de conserjería') THEN
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

-- Inserción de datos en la tabla Centro
INSERT INTO Centro (Nombre, Direccion, Telefono, Email) VALUES
('Facultad de Ciencias Económicas y Empresariales', 'Campus Lagoas-Marcosende, s/n, 36310 Vigo, Pontevedra', '986812300', 'info.fcee@uvigo.es'),
('Escuela de Ingeniería Industrial', 'Campus Universitario, 36310 Vigo, Pontevedra', '986812301', 'info.eii@uvigo.es'),
('Facultad de Biología', 'Campus Lagoas-Marcosende, s/n, 36310 Vigo, Pontevedra', '986812302', 'info.biologia@uvigo.es'),
('Escuela de Ingeniería de Telecomunicación', 'Campus Universitario, 36310 Vigo, Pontevedra', '986812303', 'info.teleco@uvigo.es'),
('Facultad de Derecho', 'Campus Lagoas-Marcosende, s/n, 36310 Vigo, Pontevedra', '986812304', 'info.derecho@uvigo.es');

-- Insertar Becarios, Conserjes, Docentes y Estudiantes para cada centro
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
-- Facultad de Ciencias Económicas y Empresariales
('12345679B', 'María', 'Fernández', '005000010001', 'maria.fernandez@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('12345680C', 'Carlos', 'Pérez', '005000010002', 'carlos.perez@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('12345681D', 'Ana', 'García', '005000010003', 'ana.garcia@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('12345682E', 'Luis', 'Rodríguez', '005000010004', 'luis.rodriguez@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('12345683F', 'Elena', 'Martínez', '005000010005', 'elena.martinez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345684G', 'Javier', 'López', '005000010006', 'javier.lopez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345685H', 'Lucía', 'González', '005000010007', 'lucia.gonzalez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345686I', 'Miguel', 'Gómez', '005000010008', 'miguel.gomez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345687J', 'Sara', 'Díaz', '005000010009', 'sara.diaz@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345688K', 'Raúl', 'Alonso', '005000010010', 'raul.alonso@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345689L', 'Marta', 'Sánchez', '005000010011', 'marta.sanchez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante'),
('12345690M', 'David', 'Jiménez', '005000010012', 'david.jimenez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345691N', 'Sandra', 'Ruiz', '005000010013', 'sandra.ruiz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345692O', 'Alberto', 'Hernández', '005000010014', 'alberto.hernandez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345693P', 'Cristina', 'Torres', '005000010015', 'cristina.torres@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345694Q', 'Jesús', 'Navarro', '005000010016', 'jesus.navarro@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345695R', 'Pilar', 'Ramos', '005000010017', 'pilar.ramos@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345696S', 'Diego', 'Santos', '005000010018', 'diego.santos@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345697T', 'Laura', 'Castro', '005000010019', 'laura.castro@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345698U', 'Andrés', 'Iglesias', '005000010020', 'andres.iglesias@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1');

-- Facultad de Escuela de Ingeniería Industrial
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('45244882H', 'Adelina', 'Gimenez', '005000010021', 'adelina.gimenez@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('35495579R', 'Luis Alfonso', 'Rios', '005000010022', 'luisalfonso.rios@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('72491235L', 'Antonio Jose', 'Gomis', '005000010023', 'antoniojose.gomi@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('25231740G', 'Natalia', 'Seoane', '005000010024', 'natalia.seoane@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('14213001J', 'Iria', 'Moncho', '005000010025', 'iria.moncho@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12345687G', 'Carlos', 'González', '005000010026', 'carlos.gonzalez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('06188375H', 'Sabela', 'Armesto', '005000010027', 'sabela.armesto@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('96766352Q', 'Miguel', 'Rodríguez', '005000010028', 'miguel.rodriguez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('13932406H', 'Bruno', 'Cruz', '005000010029', 'bruno.cruz@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('35406058L', 'Daniel', 'Yañez', '005000010030', 'daniel.yanez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('74730186N', 'Isabel', 'Ferro', '005000010031', 'isabel.ferro@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('34138868Z', 'Macarena', 'Adrover', '005000010032', 'macarena.adrover@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('79105253L', 'Ismael', 'Vázquez', '005000010033', 'ismael.vazquez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('03243587N', 'Javier', 'Vidal', '005000010034', 'javier.vidal@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('48118464A', 'Aurora', 'Rivera', '005000010035', 'aurora.rivera@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('15063350Y', 'Rubén', 'Araujo', '005000010036', 'ruben.araujo@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('21186056C', 'Natalia', 'Fontán', '005000010037', 'natalia.fontan@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('80721965S', 'Ismael', 'Vizcaya', '005000010038', 'ismael.vizcaya@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('10043774L', 'Sabela', 'Rodríguez', '005000010039', 'sabela.rodriguez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('80896416B', 'Tania', 'Cuquejo', '005000010040', 'tania.cuquejo@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1');

-- Facultad de Biología
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('55678012D', 'Antonio', 'Santos', '005000010041', 'antonio.santos@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('66543210E', 'Elisa', 'Prieto', '005000010042', 'elisa.prieto@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('77490123F', 'Fernando', 'Luna', '005000010043', 'fernando.luna@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('88321234G', 'Pablo', 'Molina', '005000010044', 'pablo.molina@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('99223455H', 'Carmen', 'Ortiz', '005000010045', 'carmen.ortiz@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('10234566I', 'Isabel', 'Romero', '005000010046', 'isabel.romero@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('11345677J', 'Tomás', 'Pérez', '005000010047', 'tomas.perez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('12456788K', 'Rosa', 'Campos', '005000010048', 'rosa.campos@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('13567899L', 'Juan', 'Ibáñez', '005000010049', 'juan.ibanez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('14678900M', 'Sofía', 'Fernández', '005000010050', 'sofia.fernandez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('15789011N', 'Francisco', 'García', '005000010051', 'francisco.garcia@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('16890122O', 'Ángela', 'Martínez', '005000010052', 'angela.martinez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('17901233P', 'Sergio', 'López', '005000010053', 'sergio.lopez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('19012344Q', 'Paula', 'González', '005000010054', 'paula.gonzalez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('20123455R', 'Manuel', 'Rodríguez', '005000010055', 'manuel.rodriguez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('21234566S', 'Lucía', 'Díaz', '005000010056', 'lucia.diaz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('22345677T', 'Alejandro', 'Moreno', '005000010057', 'alejandro.moreno@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('23456788U', 'Beatriz', 'Ruiz', '005000010058', 'beatriz.ruiz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('24567899V', 'Raquel', 'Ramos', '005000010059', 'raquel.ramos@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('25678900W', 'Jorge', 'Martín', '005000010060', 'jorge.martin@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1');

-- Escuela de Ingeniería de Telecomunicación
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('98765432A', 'Patricia', 'Silva', '005000010061', 'patricia.silva@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('87654321B', 'Marcos', 'Cabrera', '005000010062', 'marcos.cabrera@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('76543210C', 'Cristina', 'Mejía', '005000010063', 'cristina.mejia@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('65432109D', 'Gabriel', 'Pardo', '005000010064', 'gabriel.pardo@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('54321098E', 'Verónica', 'Sánchez', '005000010065', 'veronica.sanchez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('43210987F', 'Miguel', 'Hernández', '005000010066', 'miguel.hernandez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('32109876G', 'Esther', 'Vega', '005000010067', 'esther.vega@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('21098765H', 'Ramón', 'Lara', '005000010068', 'ramon.lara@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('10987654I', 'Clara', 'Gallego', '005000010069', 'clara.gallego@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('09876543J', 'Víctor', 'Serrano', '005000010070', 'victor.serrano@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('08765432K', 'Alba', 'Navarro', '005000010071', 'alba.navarro@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('07654321L', 'Carlos', 'Gómez', '005000010072', 'carlos.gomez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('06543210M', 'Julia', 'Fernández', '005000010073', 'julia.fernandez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('05432109N', 'David', 'Torres', '005000010074', 'david.torres@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('04321098O', 'Elena', 'Rivas', '005000010075', 'elena.rivas@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('03210987P', 'Roberto', 'Gil', '005000010076', 'roberto.gil@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('02109876Q', 'Laura', 'Alonso', '005000010077', 'laura.alonso@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('01098765R', 'José', 'Pérez', '005000010078', 'jose.perez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('99987654S', 'Lorena', 'Muñoz', '005000010079', 'lorena.munoz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('88876543T', 'Emilio', 'Herrera', '005000010080', 'emilio.herrera@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1');

-- Facultad de Derecho
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('12349876A', 'Daniel', 'Nieto', '005000010081', 'daniel.nieto@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('23456789B', 'Adriana', 'Marín', '005000010082', 'adriana.marin@uvigo.es', 'Becario de infraestructura', 'ContrasenaBecario1'),
('34567890C', 'Pedro', 'Guzmán', '005000010083', 'pedro.guzman@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('45678901D', 'Luisa', 'Pérez', '005000010084', 'luisa.perez@uvigo.es', 'Personal de conserjeria', 'ContrasenaConserje1'),
('56789012E', 'Esteban', 'Morales', '005000010085', 'esteban.morales@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('67890123F', 'Marta', 'López', '005000010086', 'marta.lopez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('78901234G', 'Alfonso', 'Méndez', '005000010087', 'alfonso.mendez@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('89012345H', 'Natalia', 'Cordero', '005000010088', 'natalia.cordero@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('90123456I', 'Rubén', 'Castro', '005000010089', 'ruben.castro@uvigo.es', 'Docente', 'ContrasenaDocente1'),
('01234567J', 'Alejandra', 'Romero', '005000010090', 'alejandra.romero@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('12345678K', 'Sergio', 'Palacios', '005000010091', 'sergio.palacios@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('23456789L', 'Verónica', 'Bermúdez', '005000010092', 'veronica.bermudez@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('34567890M', 'Miguel', 'Ortiz', '005000010093', 'miguel.ortiz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('45678901N', 'Laura', 'Molina', '005000010094', 'laura.molina@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('56789012O', 'Jorge', 'Soto', '005000010095', 'jorge.soto@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('67890123P', 'Paola', 'León', '005000010096', 'paola.leon@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('78901234Q', 'Ignacio', 'Bravo', '005000010097', 'ignacio.bravo@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('89012345R', 'Claudia', 'Moreno', '005000010098', 'claudia.moreno@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('90123456S', 'Felipe', 'Vera', '005000010099', 'felipe.vera@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1'),
('01234567T', 'Daniela', 'Cruz', '005000010100', 'daniela.cruz@uvigo.es', 'Estudiante', 'ContrasenaEstudiante1');

-- Insertar Admin
INSERT INTO Usuario (DNI, Nombre, Apellidos, NIU, Email, Rol, Contrasena) VALUES
('admin', 'Admin', 'Admin', '005000099999', 'admin@uvigo.es', 'Admin', 'ContrasenaAdmin1');

-- Insertar Recursos
-- Facultad de Ciencias Económicas y Empresariales
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude', 'Disponible', 1),
('Portátil', 'Portátil HP Pavilion', 'Disponible', 1),
('Aula', 'Aula 101', 'Disponible', 1),
('Aula', 'Aula 102', 'Disponible', 1),
('Aula', 'Aula 103', 'Disponible', 1),
('Aula', 'Aula 104', 'Disponible', 1),
('Aula', 'Aula 105', 'Disponible', 1),
('Seminario', 'Seminario 21', 'Disponible', 1),
('Seminario', 'Seminario 22', 'Disponible', 1),
('Seminario', 'Seminario 23', 'Disponible', 1),
('Sala de Juntas', 'Sala de Juntas 1', 'Disponible', 1),
('Sala de Juntas', 'Sala de Juntas 2', 'Disponible', 1),
('Salón de Actos', 'Salón de Actos Principal', 'Disponible', 1),
('Laboratorio', 'Laboratorio de Informática', 'Disponible', 1);

-- Facultad de Escuela de Ingeniería Industrial
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude', 'Disponible', 2),
('Portátil', 'Portátil HP Pavilion', 'Disponible', 2),
('Aula', 'Aula 101', 'Disponible', 2),
('Aula', 'Aula 102', 'Disponible', 2),
('Aula', 'Aula 103', 'Disponible', 2),
('Aula', 'Aula 104', 'Disponible', 2),
('Aula', 'Aula 105', 'Disponible', 2),
('Seminario', 'Seminario 21', 'Disponible', 2),
('Seminario', 'Seminario 22', 'Disponible', 2),
('Seminario', 'Seminario 23', 'Disponible', 2),
('Sala de Juntas', 'Sala de Juntas 1', 'Disponible', 2),
('Sala de Juntas', 'Sala de Juntas 2', 'Disponible', 2),
('Salón de Actos', 'Salón de Actos Principal', 'Disponible', 2),
('Laboratorio', 'Laboratorio de Informática', 'Disponible', 2);

-- Facultad de Escuela de Facultad de Biología
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude', 'Disponible', 3),
('Portátil', 'Portátil HP Pavilion', 'Disponible', 3),
('Aula', 'Aula 101', 'Disponible', 3),
('Aula', 'Aula 102', 'Disponible', 3),
('Aula', 'Aula 103', 'Disponible', 3),
('Aula', 'Aula 104', 'Disponible', 3),
('Aula', 'Aula 105', 'Disponible', 3),
('Seminario', 'Seminario 21', 'Disponible', 3),
('Seminario', 'Seminario 22', 'Disponible', 3),
('Seminario', 'Seminario 23', 'Disponible', 3),
('Sala de Juntas', 'Sala de Juntas 1', 'Disponible', 3),
('Sala de Juntas', 'Sala de Juntas 2', 'Disponible', 3),
('Salón de Actos', 'Salón de Actos Principal', 'Disponible', 3),
('Laboratorio', 'Laboratorio de Informática', 'Disponible', 3);

-- Facultad de Escuela de Escuela de Ingeniería de Telecomunicación
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude', 'Disponible', 4),
('Portátil', 'Portátil HP Pavilion', 'Disponible', 4),
('Aula', 'Aula 101', 'Disponible', 4),
('Aula', 'Aula 102', 'Disponible', 4),
('Aula', 'Aula 103', 'Disponible', 4),
('Aula', 'Aula 104', 'Disponible', 4),
('Aula', 'Aula 105', 'Disponible', 4),
('Seminario', 'Seminario 21', 'Disponible', 4),
('Seminario', 'Seminario 22', 'Disponible', 4),
('Seminario', 'Seminario 23', 'Disponible', 4),
('Sala de Juntas', 'Sala de Juntas 1', 'Disponible', 4),
('Sala de Juntas', 'Sala de Juntas 2', 'Disponible', 4),
('Salón de Actos', 'Salón de Actos Principal', 'Disponible', 4),
('Laboratorio', 'Laboratorio de Informática', 'Disponible', 4);

-- Facultad de Escuela de Facultad de Derecho
INSERT INTO Recurso (Tipo, Descripcion, Disponibilidad, ID_Centro) VALUES
('Portátil', 'Portátil Dell Latitude', 'Disponible', 5),
('Portátil', 'Portátil HP Pavilion', 'Disponible', 5),
('Aula', 'Aula 101', 'Disponible', 5),
('Aula', 'Aula 102', 'Disponible', 5),
('Aula', 'Aula 103', 'Disponible', 5),
('Aula', 'Aula 104', 'Disponible', 5),
('Aula', 'Aula 105', 'Disponible', 5),
('Seminario', 'Seminario 21', 'Disponible', 5),
('Seminario', 'Seminario 22', 'Disponible', 5),
('Seminario', 'Seminario 23', 'Disponible', 5),
('Sala de Juntas', 'Sala de Juntas 1', 'Disponible', 5),
('Sala de Juntas', 'Sala de Juntas 2', 'Disponible', 5),
('Salón de Actos', 'Salón de Actos Principal', 'Disponible', 5),
('Laboratorio', 'Laboratorio de Informática', 'Disponible', 5);

-- Asignar Becarios y Conserjes a Centros
-- Facultad de Ciencias Económicas y Empresariales
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(1, 1), -- María Fernández
(1, 2); -- Carlos Pérez

INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(1, 3), -- Ana García
(1, 4); -- Luis Rodríguez

-- Facultad de Escuela de Ingeniería Industrial
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(2, 21), -- Adelina Gimenez
(2, 22); -- Luis Alfonso Rios

INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(2, 23), -- Antonio Jose Gomis
(2, 24); -- Natalia Seoane

-- Facultad de Biología
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(3, 41), -- Antonio Santos
(3, 42); -- Elisa Prieto

INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(3, 43), -- Fernando Luna
(3, 44); -- Pablo Molina

-- Escuela de Ingeniería de Telecomunicación
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(4, 61), -- Patricia Silva
(4, 62); -- Marcos Cabrera

INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(4, 63), -- Cristina Mejía
(4, 64); -- Gabriel Pardo

-- Facultad de Derecho
INSERT INTO Centro_Becario (ID_Centro, ID_Usuario) VALUES
(4, 81), -- Daniel Nieto
(4, 82); -- Adriana Marín

INSERT INTO Centro_Conserje (ID_Centro, ID_Usuario) VALUES
(5, 83), -- Pedro Guzmán 
(5, 84); -- Luisa Pérez 

-- Insertar Reservas
-- Reservas en Facultad de Ciencias Económicas y Empresariales
INSERT INTO Reserva (ID_Usuario, ID_Recurso, Fecha_Hora_Reserva, ID_Franja, Estado) VALUES
(10, 1, '2024-09-01 08:30:00', 1, 'Confirmada'), -- Raúl Alonso reserva un portátil
(11, 2, '2024-09-01 10:30:00', 2, 'No Confirmada'); -- Marta Sánchez reserva otro portátil

-- Insertar Incidencias
-- Incidencias en Facultad de Ciencias Económicas y Empresariales
INSERT INTO Incidencia (ID_Usuario, ID_Recurso, Descripcion_Problema, Fecha_Reporte, Estado) VALUES
(10, 1, 'Portátil no enciende', '2024-09-02 09:00:00', 'Pendiente'), -- Raúl Alonso reporta un problema
(11, 2, 'Problema con el teclado del portátil', '2024-09-02 11:00:00', 'Pendiente'); -- Marta Sánchez reporta otro problema

-- Asignaciones coherentes a becarios y conserjes del mismo centro
INSERT INTO Incidencia_Asignacion (ID_Incidencia, ID_Usuario) VALUES
(1, 1), -- María Fernández asignada a la incidencia 1
(2, 2); -- Carlos Pérez asignado a la incidencia 2

COMMIT;
