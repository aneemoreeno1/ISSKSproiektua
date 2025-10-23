-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 16-09-2020 a las 16:37:17
-- Versión del servidor: 10.5.5-MariaDB-1:10.5.5+maria~focal
-- Versión de PHP: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `database`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `nombre` text NOT NULL,
  `nan` varchar(10) NOT NULL,
  `telefonoa` varchar(9) NOT NULL,
  `jaiotze_data` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `pasahitza` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`nombre`, `nan`, `telefonoa`, `jaiotze_data`, `email`, `pasahitza`) VALUES
('mikel', '12345678-A', '123456789', '1990-01-01', 'mikel@adibide.com', 'pasahitz1'),
('aitor', '87654321-B', '987654321', '1992-02-02', 'aitor@adibide.com', 'pasahitz2');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementuak`
--

CREATE TABLE `pelikula` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `izena` varchar(100) NOT NULL,
  `deskribapena` text,
  `urtea` int(11),
  `egilea` varchar(100),
  `generoa` varchar(50),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `elementuak`
--

INSERT INTO `pelikula` (`izena`, `deskribapena`, `urtea`, `egilea`, `generoa`) VALUES
('Harry Potter', 'Magia munduko abenturak', 1997, 'J.K. Rowling', 'Fantasia'),
('1984', 'Distopia eleberri ospetsua', 1949, 'George Orwell', 'Zientzia-fikzioa'),
('The Godfather', 'Mafia familia baten istorioa', 1972, 'Mario Puzo', 'Drama');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

