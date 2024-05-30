-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-01-2020 a las 18:24:43
-- Versión del servidor: 10.4.6-MariaDB
-- Versión de PHP: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `UVigoRes`
--

DROP SCHEMA IF EXISTS `UVigoRes`;

CREATE DATABASE IF NOT EXISTS `UVigoRes` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `UVigoRes`;

-- Creación de la tabla Usuario
CREATE TABLE `usuario` (
    `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
    `niu` VARCHAR(50) NOT NULL,
    `nombre` VARCHAR(50) NOT NULL,
    `apellidos` VARCHAR(50) NOT NULL,
    `dni` VARCHAR(9) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `rol` ENUM('Alumno', 'Becario', 'Administrador') NOT NULL,
    `contrasena` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Creación de la tabla Recurso
CREATE TABLE `recurso` (
    `id_recurso` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('Portátil', 'Seminario', 'Aula') NOT NULL,
    `descripcion` TEXT NOT NULL,
    `disponibilidad` ENUM('Disponible', 'No disponible') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Creación de la tabla Reserva
CREATE TABLE `reserva` (
    `id_reserva` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `id_recurso` INT NOT NULL,
    `fecha_reserva` DATETIME NOT NULL,
    `codigo_qr` VARCHAR(100) NOT NULL,
    `estado` ENUM('Confirmada', 'No Confirmada') NOT NULL,
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`),
    FOREIGN KEY (`id_recurso`) REFERENCES `recurso`(`id_recurso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Creación de la tabla Incidencia
CREATE TABLE `incidencia` (
    `id_incidencia` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `id_recurso` INT NOT NULL,
    `descripcion_incidencia` TEXT NOT NULL,
    `fecha_incidencia` DATETIME NOT NULL,
    `estado` ENUM('Pendiente', 'Resuelta') NOT NULL,
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`),
    FOREIGN KEY (`id_recurso`) REFERENCES `recurso`(`id_recurso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Creación de la tabla Penalizacion
CREATE TABLE `penalizacion` (
    `id_penalizacion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_usuario` INT NOT NULL,
    `fecha_inicio_penalizacion` DATE NOT NULL,
    `fecha_fin_penalizacion` DATE NOT NULL,
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario`(`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Inserción de datos de ejemplo

INSERT INTO `usuario` (`id_usuario`, `niu`, `nombre`, `apellidos`, `dni`, `email`, `rol`, `contrasena`) VALUES
(1, '123456', 'Juan', 'Pérez', '12345678A', 'juan.perez@uvigo.es', 'Alumno', 'password1'),
(2, '234567', 'Ana', 'García', '23456789B', 'ana.garcia@uvigo.es', 'Becario', 'password2'),
(3, '345678', 'Luis', 'Martínez', '34567890C', 'luis.martinez@uvigo.es', 'Administrador', 'password3'),
(4, '456789', 'Carlos', 'Sánchez', '45678901D', 'carlos.sanchez@uvigo.es', 'Alumno', 'password4'),
(5, '567890', 'María', 'López', '56789012E', 'maria.lopez@uvigo.es', 'Alumno', 'password5'),
(6, '678901', 'José', 'González', '67890123F', 'jose.gonzalez@uvigo.es', 'Becario', 'password6'),
(7, '789012', 'Laura', 'Rodríguez', '78901234G', 'laura.rodriguez@uvigo.es', 'Alumno', 'password7'),
(8, '890123', 'Miguel', 'Fernández', '89012345H', 'miguel.fernandez@uvigo.es', 'Administrador', 'password8'),
(9, '901234', 'Elena', 'Díaz', '90123456I', 'elena.diaz@uvigo.es', 'Alumno', 'password9'),
(10, '012345', 'Pedro', 'Moreno', '01234567J', 'pedro.moreno@uvigo.es', 'Becario', 'password10'),
(11, '111111', 'Sara', 'Jiménez', '11111111K', 'sara.jimenez@uvigo.es', 'Alumno', 'password11'),
(12, '222222', 'David', 'Hernández', '22222222L', 'david.hernandez@uvigo.es', 'Administrador', 'password12'),
(13, '333333', 'Marta', 'Ruiz', '33333333M', 'marta.ruiz@uvigo.es', 'Alumno', 'password13'),
(14, '444444', 'Javier', 'Gómez', '44444444N', 'javier.gomez@uvigo.es', 'Becario', 'password14'),
(15, '555555', 'Carmen', 'Castro', '55555555O', 'carmen.castro@uvigo.es', 'Alumno', 'password15'),
(16, '666666', 'Manuel', 'Vázquez', '66666666P', 'manuel.vazquez@uvigo.es', 'Administrador', 'password16'),
(17, '777777', 'Patricia', 'Ramos', '77777777Q', 'patricia.ramos@uvigo.es', 'Alumno', 'password17'),
(18, '888888', 'Francisco', 'Blanco', '88888888R', 'francisco.blanco@uvigo.es', 'Becario', 'password18'),
(19, '999999', 'Clara', 'Molina', '99999999S', 'clara.molina@uvigo.es', 'Alumno', 'password19'),
(20, '000000', 'Alberto', 'Santos', '00000000T', 'alberto.santos@uvigo.es', 'Administrador', 'password20'),
(21, '111222', 'Isabel', 'Ortega', '11122233U', 'isabel.ortega@uvigo.es', 'Alumno', 'password21'),
(22, '222333', 'Victor', 'Torres', '22233344V', 'victor.torres@uvigo.es', 'Becario', 'password22'),
(23, '333444', 'Raquel', 'Gil', '33344455W', 'raquel.gil@uvigo.es', 'Alumno', 'password23'),
(24, '444555', 'Hugo', 'Ramírez', '44455566X', 'hugo.ramirez@uvigo.es', 'Administrador', 'password24'),
(25, '555666', 'Adriana', 'Martín', '55566677Y', 'adriana.martin@uvigo.es', 'Alumno', 'password25'),
(26, '666777', 'Ricardo', 'Ferrer', '66677788Z', 'ricardo.ferrer@uvigo.es', 'Becario', 'password26'),
(27, '777888', 'Lorena', 'Navarro', '77788899A', 'lorena.navarro@uvigo.es', 'Alumno', 'password27'),
(28, '888999', 'Tomás', 'Reyes', '88899900B', 'tomas.reyes@uvigo.es', 'Administrador', 'password28'),
(29, '999000', 'Beatriz', 'Pardo', '99900011C', 'beatriz.pardo@uvigo.es', 'Alumno', 'password29'),
(30, '000111', 'Álvaro', 'Cabrera', '00011122D', 'alvaro.cabrera@uvigo.es', 'Becario', 'password30'),
(31, '111333', 'Olga', 'Serrano', '11133344E', 'olga.serrano@uvigo.es', 'Alumno', 'password31'),
(32, '222444', 'Mario', 'Iglesias', '22244455F', 'mario.iglesias@uvigo.es', 'Administrador', 'password32'),
(33, '333555', 'Natalia', 'Bravo', '33355566G', 'natalia.bravo@uvigo.es', 'Alumno', 'password33'),
(34, '444666', 'Felipe', 'Rivas', '44466677H', 'felipe.rivas@uvigo.es', 'Becario', 'password34'),
(35, '555777', 'Inés', 'Pascual', '55577788I', 'ines.pascual@uvigo.es', 'Alumno', 'password35'),
(36, '666888', 'Pablo', 'Sáez', '66688899J', 'pablo.saez@uvigo.es', 'Administrador', 'password36'),
(37, '777999', 'Silvia', 'Esteban', '77799900K', 'silvia.esteban@uvigo.es', 'Alumno', 'password37'),
(38, '888000', 'Gabriel', 'Nieto', '88800011L', 'gabriel.nieto@uvigo.es', 'Becario', 'password38'),
(39, '999111', 'Susana', 'Rubio', '99911122M', 'susana.rubio@uvigo.es', 'Alumno', 'password39'),
(40, '000222', 'Rafael', 'Soto', '00022233N', 'rafael.soto@uvigo.es', 'Administrador', 'password40'),
(41, '111444', 'Andrea', 'Méndez', '11144455O', 'andrea.mendez@uvigo.es', 'Alumno', 'password41'),
(42, '222555', 'Guillermo', 'Peña', '22255566P', 'guillermo.pena@uvigo.es', 'Becario', 'password42'),
(43, '333666', 'Verónica', 'Cruz', '33366677Q', 'veronica.cruz@uvigo.es', 'Alumno', 'password43'),
(44, '444777', 'Sergio', 'Delgado', '44477788R', 'sergio.delgado@uvigo.es', 'Administrador', 'password44'),
(45, '555888', 'Lidia', 'Campos', '55588899S', 'lidia.campos@uvigo.es', 'Alumno', 'password45'),
(46, '666999', 'Juan', 'Muñoz', '66699900T', 'juan.munoz@uvigo.es', 'Becario', 'password46'),
(47, '777000', 'Natalia', 'Vega', '77700011U', 'natalia.vega@uvigo.es', 'Alumno', 'password47'),
(48, '888111', 'Santiago', 'Domínguez', '88811122V', 'santiago.dominguez@uvigo.es', 'Administrador', 'password48'),
(49, '999222', 'Mónica', 'Lara', '99922233W', 'monica.lara@uvigo.es', 'Alumno', 'password49'),
(50, '000333', 'Enrique', 'Cano', '00033344X', 'enrique.cano@uvigo.es', 'Becario', 'password50');

INSERT INTO `recurso` (`id_recurso`, `tipo`, `descripcion`, `disponibilidad`) VALUES
(1, 'Portátil', 'Portátil HP 15"', 'Disponible'),
(2, 'Portátil', 'Portátil Dell Inspiron', 'No disponible'),
(3, 'Portátil', 'Portátil Lenovo ThinkPad', 'Disponible'),
(4, 'Portátil', 'Portátil Acer Aspire', 'No disponible'),
(5, 'Portátil', 'Portátil MacBook Air', 'Disponible'),
(6, 'Portátil', 'Portátil ASUS VivoBook', 'No disponible'),
(7, 'Portátil', 'Portátil Microsoft Surface', 'Disponible'),
(8, 'Portátil', 'Portátil Toshiba Satellite', 'No disponible'),
(9, 'Portátil', 'Portátil Samsung Notebook', 'Disponible'),
(10, 'Portátil', 'Portátil LG Gram', 'No disponible'),
(11, 'Portátil', 'Portátil MSI Prestige', 'Disponible'),
(12, 'Portátil', 'Portátil Huawei MateBook', 'No disponible'),
(13, 'Portátil', 'Portátil Razer Blade', 'Disponible'),
(14, 'Portátil', 'Portátil Google Pixelbook', 'No disponible'),
(15, 'Portátil', 'Portátil HP Spectre', 'Disponible'),
(16, 'Portátil', 'Portátil Dell XPS', 'No disponible'),
(17, 'Portátil', 'Portátil Lenovo Yoga', 'Disponible'),
(18, 'Portátil', 'Portátil Acer Swift', 'No disponible'),
(19, 'Portátil', 'Portátil MacBook Pro', 'Disponible'),
(20, 'Portátil', 'Portátil ASUS ZenBook', 'No disponible'),
(21, 'Portátil', 'Portátil Microsoft Surface Laptop', 'Disponible'),
(22, 'Portátil', 'Portátil Toshiba Tecra', 'No disponible'),
(23, 'Portátil', 'Portátil Samsung Galaxy Book', 'Disponible'),
(24, 'Portátil', 'Portátil LG Ultra', 'No disponible'),
(25, 'Portátil', 'Portátil MSI Modern', 'Disponible'),
(26, 'Seminario', 'Seminario sobre IA', 'Disponible'),
(27, 'Seminario', 'Seminario de Matemáticas', 'No disponible'),
(28, 'Seminario', 'Seminario de Física', 'Disponible'),
(29, 'Seminario', 'Seminario de Química', 'No disponible'),
(30, 'Seminario', 'Seminario de Biología', 'Disponible'),
(31, 'Seminario', 'Seminario de Historia', 'No disponible'),
(32, 'Seminario', 'Seminario de Filosofía', 'Disponible'),
(33, 'Seminario', 'Seminario de Literatura', 'No disponible'),
(34, 'Seminario', 'Seminario de Música', 'Disponible'),
(35, 'Seminario', 'Seminario de Arte', 'No disponible'),
(36, 'Seminario', 'Seminario de Ingeniería', 'Disponible'),
(37, 'Seminario', 'Seminario de Medicina', 'No disponible'),
(38, 'Seminario', 'Seminario de Derecho', 'Disponible'),
(39, 'Seminario', 'Seminario de Economía', 'No disponible'),
(40, 'Seminario', 'Seminario de Psicología', 'Disponible'),
(41, 'Aula', 'Aula 101', 'Disponible'),
(42, 'Aula', 'Aula 102', 'No disponible'),
(43, 'Aula', 'Aula 103', 'Disponible'),
(44, 'Aula', 'Aula 104', 'No disponible'),
(45, 'Aula', 'Aula 105', 'Disponible'),
(46, 'Aula', 'Aula 201', 'No disponible'),
(47, 'Aula', 'Aula 202', 'Disponible'),
(48, 'Aula', 'Aula 203', 'No disponible'),
(49, 'Aula', 'Aula 204', 'Disponible'),
(50, 'Aula', 'Aula 205', 'No disponible');

INSERT INTO `reserva` (`id_reserva`, `id_usuario`, `id_recurso`, `fecha_reserva`, `codigo_qr`, `estado`) VALUES
(1, 1, 1, '2024-06-01 10:00:00', 'QR12345', 'Confirmada'),
(2, 2, 2, '2024-06-02 12:00:00', 'QR23456', 'No Confirmada'),
(3, 3, 3, '2024-06-03 14:00:00', 'QR34567', 'Confirmada'),
(4, 4, 4, '2024-06-04 16:00:00', 'QR45678', 'No Confirmada'),
(5, 5, 5, '2024-06-05 18:00:00', 'QR56789', 'Confirmada'),
(6, 6, 6, '2024-06-06 10:00:00', 'QR67890', 'No Confirmada'),
(7, 7, 7, '2024-06-07 12:00:00', 'QR78901', 'Confirmada'),
(8, 8, 8, '2024-06-08 14:00:00', 'QR89012', 'No Confirmada'),
(9, 9, 9, '2024-06-09 16:00:00', 'QR90123', 'Confirmada'),
(10, 10, 10, '2024-06-10 18:00:00', 'QR01234', 'No Confirmada'),
(11, 11, 11, '2024-06-11 10:00:00', 'QR12346', 'Confirmada'),
(12, 12, 12, '2024-06-12 12:00:00', 'QR23457', 'No Confirmada'),
(13, 13, 13, '2024-06-13 14:00:00', 'QR34568', 'Confirmada'),
(14, 14, 14, '2024-06-14 16:00:00', 'QR45679', 'No Confirmada'),
(15, 15, 15, '2024-06-15 18:00:00', 'QR56780', 'Confirmada'),
(16, 16, 16, '2024-06-16 10:00:00', 'QR67891', 'No Confirmada'),
(17, 17, 17, '2024-06-17 12:00:00', 'QR78902', 'Confirmada'),
(18, 18, 18, '2024-06-18 14:00:00', 'QR89013', 'No Confirmada'),
(19, 19, 19, '2024-06-19 16:00:00', 'QR90124', 'Confirmada'),
(20, 20, 20, '2024-06-20 18:00:00', 'QR01235', 'No Confirmada'),
(21, 21, 21, '2024-06-21 10:00:00', 'QR12347', 'Confirmada'),
(22, 22, 22, '2024-06-22 12:00:00', 'QR23458', 'No Confirmada'),
(23, 23, 23, '2024-06-23 14:00:00', 'QR34569', 'Confirmada'),
(24, 24, 24, '2024-06-24 16:00:00', 'QR45670', 'No Confirmada'),
(25, 25, 25, '2024-06-25 18:00:00', 'QR56781', 'Confirmada'),
(26, 26, 26, '2024-06-26 10:00:00', 'QR67892', 'No Confirmada'),
(27, 27, 27, '2024-06-27 12:00:00', 'QR78903', 'Confirmada'),
(28, 28, 28, '2024-06-28 14:00:00', 'QR89014', 'No Confirmada'),
(29, 29, 29, '2024-06-29 16:00:00', 'QR90125', 'Confirmada'),
(30, 30, 30, '2024-06-30 18:00:00', 'QR01236', 'No Confirmada'),
(31, 31, 31, '2024-07-01 10:00:00', 'QR12348', 'Confirmada'),
(32, 32, 32, '2024-07-02 12:00:00', 'QR23459', 'No Confirmada'),
(33, 33, 33, '2024-07-03 14:00:00', 'QR34560', 'Confirmada'),
(34, 34, 34, '2024-07-04 16:00:00', 'QR45671', 'No Confirmada'),
(35, 35, 35, '2024-07-05 18:00:00', 'QR56782', 'Confirmada'),
(36, 36, 36, '2024-07-06 10:00:00', 'QR67893', 'No Confirmada'),
(37, 37, 37, '2024-07-07 12:00:00', 'QR78904', 'Confirmada'),
(38, 38, 38, '2024-07-08 14:00:00', 'QR89015', 'No Confirmada'),
(39, 39, 39, '2024-07-09 16:00:00', 'QR90126', 'Confirmada'),
(40, 40, 40, '2024-07-10 18:00:00', 'QR01237', 'No Confirmada'),
(41, 41, 41, '2024-07-11 10:00:00', 'QR12349', 'Confirmada'),
(42, 42, 42, '2024-07-12 12:00:00', 'QR23460', 'No Confirmada'),
(43, 43, 43, '2024-07-13 14:00:00', 'QR34561', 'Confirmada'),
(44, 44, 44, '2024-07-14 16:00:00', 'QR45672', 'No Confirmada'),
(45, 45, 45, '2024-07-15 18:00:00', 'QR56783', 'Confirmada'),
(46, 46, 46, '2024-07-16 10:00:00', 'QR67894', 'No Confirmada'),
(47, 47, 47, '2024-07-17 12:00:00', 'QR78905', 'Confirmada'),
(48, 48, 48, '2024-07-18 14:00:00', 'QR89016', 'No Confirmada'),
(49, 49, 49, '2024-07-19 16:00:00', 'QR90127', 'Confirmada'),
(50, 50, 50, '2024-07-20 18:00:00', 'QR01238', 'No Confirmada'),
(51, 1, 2, '2024-07-21 10:00:00', 'QR12350', 'Confirmada'),
(52, 2, 3, '2024-07-22 12:00:00', 'QR23461', 'No Confirmada'),
(53, 3, 4, '2024-07-23 14:00:00', 'QR34562', 'Confirmada'),
(54, 4, 5, '2024-07-24 16:00:00', 'QR45673', 'No Confirmada'),
(55, 5, 6, '2024-07-25 18:00:00', 'QR56784', 'Confirmada'),
(56, 6, 7, '2024-07-26 10:00:00', 'QR67895', 'No Confirmada'),
(57, 7, 8, '2024-07-27 12:00:00', 'QR78906', 'Confirmada'),
(58, 8, 9, '2024-07-28 14:00:00', 'QR89017', 'No Confirmada'),
(59, 9, 10, '2024-07-29 16:00:00', 'QR90128', 'Confirmada'),
(60, 10, 11, '2024-07-30 18:00:00', 'QR01239', 'No Confirmada'),
(61, 11, 12, '2024-07-31 10:00:00', 'QR12351', 'Confirmada');

INSERT INTO `incidencia` (`id_incidencia`, `id_usuario`, `id_recurso`, `descripcion_incidencia`, `fecha_incidencia`, `estado`) VALUES
(1, 1, 1, 'Pantalla dañada', '2024-06-01 11:00:00', 'Pendiente'),
(2, 2, 2, 'Proyector no funciona', '2024-06-02 12:30:00', 'Resuelta'),
(3, 3, 3, 'Teclado roto', '2024-06-03 14:45:00', 'Pendiente'),
(4, 4, 4, 'Problemas de conexión', '2024-06-04 16:20:00', 'Resuelta'),
(5, 5, 5, 'Batería defectuosa', '2024-06-05 18:10:00', 'Pendiente'),
(6, 6, 6, 'Pantalla azul', '2024-06-06 10:05:00', 'Resuelta'),
(7, 7, 7, 'Sistema operativo no arranca', '2024-06-07 12:15:00', 'Pendiente'),
(8, 8, 8, 'Altavoces no funcionan', '2024-06-08 14:30:00', 'Resuelta'),
(9, 9, 9, 'Ratón defectuoso', '2024-06-09 16:40:00', 'Pendiente'),
(10, 10, 10, 'Cámara no funciona', '2024-06-10 18:25:00', 'Resuelta'),
(11, 11, 11, 'Problemas de refrigeración', '2024-06-11 10:35:00', 'Pendiente'),
(12, 12, 12, 'Software malicioso detectado', '2024-06-12 12:50:00', 'Resuelta'),
(13, 13, 13, 'Problemas con el proyector', '2024-06-13 14:55:00', 'Pendiente'),
(14, 14, 14, 'Fallo en la conexión HDMI', '2024-06-14 16:25:00', 'Resuelta'),
(15, 15, 15, 'Problemas con la red Wi-Fi', '2024-06-15 18:35:00', 'Pendiente'),
(16, 16, 16, 'El equipo se apaga solo', '2024-06-16 10:40:00', 'Resuelta'),
(17, 17, 17, 'Pantalla parpadeante', '2024-06-17 12:45:00', 'Pendiente'),
(18, 18, 18, 'Micrófono no funciona', '2024-06-18 14:50:00', 'Resuelta'),
(19, 19, 19, 'Problemas con el teclado', '2024-06-19 16:55:00', 'Pendiente'),
(20, 20, 20, 'Sistema no inicia', '2024-06-20 18:00:00', 'Resuelta');

INSERT INTO `penalizacion` (`id_penalizacion`, `id_usuario`, `fecha_inicio_penalizacion`, `fecha_fin_penalizacion`) VALUES
(1, 1, '2024-06-01', '2024-06-07'),
(2, 2, '2024-06-02', '2024-06-08'),
(3, 3, '2024-06-03', '2024-06-09'),
(4, 4, '2024-06-04', '2024-06-10'),
(5, 5, '2024-06-05', '2024-06-11'),
(6, 6, '2024-06-06', '2024-06-12'),
(7, 7, '2024-06-07', '2024-06-13'),
(8, 8, '2024-06-08', '2024-06-14'),
(9, 9, '2024-06-09', '2024-06-15'),
(10, 10, '2024-06-10', '2024-06-16'),
(11, 11, '2024-06-11', '2024-06-17'),
(12, 12, '2024-06-12', '2024-06-18'),
(13, 13, '2024-06-13', '2024-06-19'),
(14, 14, '2024-06-14', '2024-06-20'),
(15, 15, '2024-06-15', '2024-06-21'),
(16, 16, '2024-06-16', '2024-06-22'),
(17, 17, '2024-06-17', '2024-06-23'),
(18, 18, '2024-06-18', '2024-06-24'),
(19, 19, '2024-06-19', '2024-06-25'),
(20, 20, '2024-06-20', '2024-06-26');

COMMIT;
