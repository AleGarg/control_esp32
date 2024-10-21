-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2024 a las 02:13:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_control`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_recibidos`
--

CREATE TABLE `datos_recibidos` (
  `id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_recibidos`
--

INSERT INTO `datos_recibidos` (`id`, `valor`, `fecha`) VALUES
(1, '8', '2024-10-10 19:05:58'),
(2, '3', '2024-10-10 19:08:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_recibidos_historial`
--

CREATE TABLE `datos_recibidos_historial` (
  `id` int(11) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `concatenado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datos_recibidos`
--
ALTER TABLE `datos_recibidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `datos_recibidos_historial`
--
ALTER TABLE `datos_recibidos_historial`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datos_recibidos`
--
ALTER TABLE `datos_recibidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `datos_recibidos_historial`
--
ALTER TABLE `datos_recibidos_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
