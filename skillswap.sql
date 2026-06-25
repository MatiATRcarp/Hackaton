-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-06-2026 a las 06:04:10
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `skillswap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_materias`
--

DROP TABLE IF EXISTS `alumno_materias`;
CREATE TABLE IF NOT EXISTS `alumno_materias` (
  `alumno_id` int UNSIGNED NOT NULL,
  `materia_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`alumno_id`,`materia_id`),
  KEY `materia_id` (`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumno_materias`
--

INSERT INTO `alumno_materias` (`alumno_id`, `materia_id`) VALUES
(2, 2),
(2, 12),
(2, 15),
(2, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blockchain_pagos`
--

DROP TABLE IF EXISTS `blockchain_pagos`;
CREATE TABLE IF NOT EXISTS `blockchain_pagos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `solicitud_id` int UNSIGNED NOT NULL,
  `tutor_id` int UNSIGNED NOT NULL,
  `alumno_id` int UNSIGNED NOT NULL,
  `wallet_destino` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_eth` decimal(18,8) NOT NULL DEFAULT '0.01000000',
  `tx_hash` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'hash simulado',
  `red` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sepolia Testnet (simulado)',
  `estado` enum('pendiente','confirmado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmado',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `solicitud_id` (`solicitud_id`),
  KEY `tutor_id` (`tutor_id`),
  KEY `alumno_id` (`alumno_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blockchain_pagos`
--

INSERT INTO `blockchain_pagos` (`id`, `solicitud_id`, `tutor_id`, `alumno_id`, `wallet_destino`, `monto_eth`, `tx_hash`, `red`, `estado`, `created_at`) VALUES
(1, 1, 3, 2, '', 0.01000000, '0x62f50913c2dcab95c5c4688abd4a24c650da22748c8cae92d2505430f7007d84', 'Sepolia Testnet (simulado)', 'confirmado', '2026-06-25 06:02:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

DROP TABLE IF EXISTS `chat_mensajes`;
CREATE TABLE IF NOT EXISTS `chat_mensajes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `solicitud_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `solicitud_id` (`solicitud_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `chat_mensajes`
--

INSERT INTO `chat_mensajes` (`id`, `solicitud_id`, `usuario_id`, `mensaje`, `created_at`) VALUES
(1, 1, 3, 'hola! punto de encuentro?', '2026-06-25 06:01:39'),
(2, 1, 2, 'el shopping te queda bien?', '2026-06-25 06:02:08'),
(3, 1, 3, 'si! te veo ahí', '2026-06-25 06:02:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncias`
--

DROP TABLE IF EXISTS `denuncias`;
CREATE TABLE IF NOT EXISTS `denuncias` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `denunciante_id` int UNSIGNED NOT NULL,
  `denunciado_id` int UNSIGNED NOT NULL,
  `motivo` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','resuelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `resolucion` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `denunciante_id` (`denunciante_id`),
  KEY `denunciado_id` (`denunciado_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `tutor_id` int UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('libre','ocupado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'libre',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tutor_id` (`tutor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `tutor_id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `created_at`) VALUES
(1, 3, '2026-06-26', '09:00:00', '22:00:00', 'ocupado', '2026-06-25 06:00:18');

--
-- Disparadores `horarios`
--
DROP TRIGGER IF EXISTS `trg_horario_no_solapamiento`;
DELIMITER $$
CREATE TRIGGER `trg_horario_no_solapamiento` BEFORE INSERT ON `horarios` FOR EACH ROW BEGIN
    DECLARE cant INT;
    SELECT COUNT(*) INTO cant
    FROM horarios
    WHERE tutor_id    = NEW.tutor_id
      AND fecha       = NEW.fecha
      AND (NEW.hora_inicio < hora_fin AND NEW.hora_fin > hora_inicio);
    IF cant > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El horario se solapa con uno existente.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE IF NOT EXISTS `materias` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `area`, `activa`, `created_at`) VALUES
(1, 'Álgebra y Geometría Analítica', 'Matemática', 1, '2026-06-25 05:28:44'),
(2, 'Análisis Matemático I', 'Matemática', 1, '2026-06-25 05:28:44'),
(3, 'Análisis Matemático II', 'Matemática', 1, '2026-06-25 05:28:44'),
(4, 'Cálculo Numérico', 'Matemática', 1, '2026-06-25 05:28:44'),
(5, 'Física I', 'Física', 1, '2026-06-25 05:28:44'),
(6, 'Física II', 'Física', 1, '2026-06-25 05:28:44'),
(7, 'Química General', 'Ciencias Básicas', 1, '2026-06-25 05:28:44'),
(8, 'Programación I', 'Informática', 1, '2026-06-25 05:28:44'),
(9, 'Programación II', 'Informática', 1, '2026-06-25 05:28:44'),
(10, 'Algoritmos y Estructuras de Datos', 'Informática', 1, '2026-06-25 05:28:44'),
(11, 'Base de Datos', 'Informática', 1, '2026-06-25 05:28:44'),
(12, 'Redes de Computadoras', 'Informática', 1, '2026-06-25 05:28:44'),
(13, 'Sistemas Operativos', 'Informática', 1, '2026-06-25 05:28:44'),
(14, 'Ingeniería de Software', 'Informática', 1, '2026-06-25 05:28:44'),
(15, 'Ciberseguridad', 'Informática', 1, '2026-06-25 05:28:44'),
(16, 'Lógica y Estructuras Discretas', 'Matemática', 1, '2026-06-25 05:28:44'),
(17, 'Estadística', 'Matemática', 1, '2026-06-25 05:28:44'),
(18, 'Inglés Técnico', 'Idiomas', 1, '2026-06-25 05:28:44'),
(19, 'Contabilidad', 'Administración', 1, '2026-06-25 05:28:44'),
(20, 'Economía', 'Administración', 1, '2026-06-25 05:28:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `alumno_id` int UNSIGNED NOT NULL,
  `tutor_id` int UNSIGNED NOT NULL,
  `horario_id` int UNSIGNED NOT NULL,
  `materia_id` int UNSIGNED NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('pendiente','aceptada','rechazada','cancelada','finalizada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `cancelada_por` enum('alumno','tutor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calificacion` tinyint UNSIGNED DEFAULT NULL COMMENT '1-5 estrellas, pone el alumno',
  `comentario_cal` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `alumno_id` (`alumno_id`),
  KEY `tutor_id` (`tutor_id`),
  KEY `horario_id` (`horario_id`),
  KEY `materia_id` (`materia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `alumno_id`, `tutor_id`, `horario_id`, `materia_id`, `mensaje`, `estado`, `cancelada_por`, `calificacion`, `comentario_cal`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 1, 6, 'Hola necesito ayuda con toda la materia', 'finalizada', NULL, NULL, NULL, '2026-06-25 06:01:11', '2026-06-25 06:02:39');

--
-- Disparadores `solicitudes`
--
DROP TRIGGER IF EXISTS `trg_solicitud_aceptada`;
DELIMITER $$
CREATE TRIGGER `trg_solicitud_aceptada` AFTER UPDATE ON `solicitudes` FOR EACH ROW BEGIN
    IF NEW.estado = 'aceptada' AND OLD.estado = 'pendiente' THEN
        UPDATE horarios SET estado = 'ocupado' WHERE id = NEW.horario_id;
    END IF;

    -- Al cancelar o rechazar → liberar horario
    IF (NEW.estado IN ('cancelada','rechazada')) AND OLD.estado = 'aceptada' THEN
        UPDATE horarios SET estado = 'libre' WHERE id = NEW.horario_id;
    END IF;

    -- Al finalizar → dejar ocupado (ya pasó) pero registrar
    -- (el estado 'ocupado' permanece; el tutor puede borrar el slot si quiere)
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutor_materias`
--

DROP TABLE IF EXISTS `tutor_materias`;
CREATE TABLE IF NOT EXISTS `tutor_materias` (
  `tutor_id` int UNSIGNED NOT NULL,
  `materia_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`tutor_id`,`materia_id`),
  KEY `materia_id` (`materia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tutor_materias`
--

INSERT INTO `tutor_materias` (`tutor_id`, `materia_id`) VALUES
(3, 5),
(3, 6),
(3, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','tutor','alumno') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'alumno',
  `estado` enum('pendiente','activo','bloqueado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `carrera` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `anio_cursado` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `universidad` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `certificado_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ruta al archivo subido',
  `wallet_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'wallet simulada Ethereum',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `estado`, `carrera`, `anio_cursado`, `universidad`, `certificado_path`, `wallet_address`, `bio`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@skillswap.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'activo', '', 0, '', NULL, NULL, NULL, '2026-06-25 05:28:43', '2026-06-25 05:28:43'),
(2, 'Lucila Montuori', 'jefe@talenthub.com', '$2y$10$Jj3wONKF8tbEwXp83eUhUOc1PPw9BsoUC2yL3IJG8Glz/tSTkuZ66', 'alumno', 'activo', 'Ingeniería en Sistemas', 2, 'Universidad Champagnat', NULL, NULL, NULL, '2026-06-25 05:32:19', '2026-06-25 05:32:19'),
(3, 'Marmot Hormigota', 'jefe1@talenthub.com', '$2y$10$e9wWaGUZCaMnU/dekOYjeOHNqVh2PZuOnfXn7rsrXwsrzKK9DxXC2', 'tutor', 'activo', 'Bioquímica', 4, 'Universidad Nacional de Cuyo', 'uploads/certificados/cert_6a3cc369bdd453.35072676.png', '', 'Hola! Mi nombre es Marmot y soy tutor especializado en Ciencias.\r\nSoy muy paciente y conozco muchas técnicas de estudio que pueden ayudar, además de manejar de manera excelente el contenido que enseño.', '2026-06-25 05:58:01', '2026-06-25 05:58:22');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno_materias`
--
ALTER TABLE `alumno_materias`
  ADD CONSTRAINT `alumno_materias_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alumno_materias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `blockchain_pagos`
--
ALTER TABLE `blockchain_pagos`
  ADD CONSTRAINT `blockchain_pagos_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `blockchain_pagos_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `blockchain_pagos_ibfk_3` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD CONSTRAINT `chat_mensajes_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_mensajes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`denunciante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `denuncias_ibfk_2` FOREIGN KEY (`denunciado_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `solicitudes_ibfk_3` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`),
  ADD CONSTRAINT `solicitudes_ibfk_4` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `tutor_materias`
--
ALTER TABLE `tutor_materias`
  ADD CONSTRAINT `tutor_materias_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tutor_materias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
