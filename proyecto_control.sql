CREATE DATABASE IF NOT EXISTS proyecto_control;
USE proyecto_control;

-- Configuraciones iniciales
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Crear la tabla `datos_recibidos`
CREATE TABLE IF NOT EXISTS `datos_recibidos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `valor` VARCHAR(255) NOT NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;