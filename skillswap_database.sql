-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: skillswap
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumno_materias`
--

DROP TABLE IF EXISTS `alumno_materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno_materias` (
  `alumno_id` int(10) unsigned NOT NULL,
  `materia_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alumno_id`,`materia_id`),
  KEY `fk_am_materia` (`materia_id`),
  CONSTRAINT `fk_am_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_am_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumno_materias`
--

LOCK TABLES `alumno_materias` WRITE;
/*!40000 ALTER TABLE `alumno_materias` DISABLE KEYS */;
INSERT INTO `alumno_materias` VALUES (2,2),(2,12),(2,15),(2,17);
/*!40000 ALTER TABLE `alumno_materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blockchain_pagos`
--

DROP TABLE IF EXISTS `blockchain_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blockchain_pagos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(10) unsigned NOT NULL,
  `tutor_id` int(10) unsigned NOT NULL,
  `alumno_id` int(10) unsigned NOT NULL,
  `wallet_destino` varchar(100) NOT NULL,
  `monto_eth` decimal(18,8) NOT NULL DEFAULT 0.01000000,
  `tx_hash` varchar(100) NOT NULL COMMENT 'hash simulado',
  `red` varchar(30) NOT NULL DEFAULT 'Sepolia Testnet (simulado)',
  `estado` enum('pendiente','confirmado') NOT NULL DEFAULT 'confirmado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `solicitud_id` (`solicitud_id`),
  KEY `fk_bp_tutor` (`tutor_id`),
  KEY `fk_bp_alumno` (`alumno_id`),
  CONSTRAINT `fk_bp_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_bp_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  CONSTRAINT `fk_bp_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockchain_pagos`
--

LOCK TABLES `blockchain_pagos` WRITE;
/*!40000 ALTER TABLE `blockchain_pagos` DISABLE KEYS */;
INSERT INTO `blockchain_pagos` VALUES (1,1,3,2,'',0.01000000,'0x62f50913c2dcab95c5c4688abd4a24c650da22748c8cae92d2505430f7007d84','Sepolia Testnet (simulado)','confirmado','2026-06-25 09:02:39');
/*!40000 ALTER TABLE `blockchain_pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_mensajes`
--

DROP TABLE IF EXISTS `chat_mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_mensajes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `mensaje` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_chat_solicitud` (`solicitud_id`),
  KEY `fk_chat_usuario` (`usuario_id`),
  CONSTRAINT `fk_chat_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_chat_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_mensajes`
--

LOCK TABLES `chat_mensajes` WRITE;
/*!40000 ALTER TABLE `chat_mensajes` DISABLE KEYS */;
INSERT INTO `chat_mensajes` VALUES (1,1,3,'hola! punto de encuentro?','2026-06-25 09:01:39'),(2,1,2,'el shopping te queda bien?','2026-06-25 09:02:08'),(3,1,3,'si! te veo ahí','2026-06-25 09:02:27');
/*!40000 ALTER TABLE `chat_mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `denuncias`
--

DROP TABLE IF EXISTS `denuncias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `denuncias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `denunciante_id` int(10) unsigned NOT NULL,
  `denunciado_id` int(10) unsigned NOT NULL,
  `motivo` varchar(300) NOT NULL,
  `estado` enum('pendiente','resuelta') NOT NULL DEFAULT 'pendiente',
  `resolucion` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_den_denunciante` (`denunciante_id`),
  KEY `fk_den_denunciado` (`denunciado_id`),
  CONSTRAINT `fk_den_denunciado` FOREIGN KEY (`denunciado_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_den_denunciante` FOREIGN KEY (`denunciante_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `denuncias`
--

LOCK TABLES `denuncias` WRITE;
/*!40000 ALTER TABLE `denuncias` DISABLE KEYS */;
/*!40000 ALTER TABLE `denuncias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tutor_id` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('libre','ocupado') NOT NULL DEFAULT 'libre',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_hor_tutor` (`tutor_id`),
  CONSTRAINT `fk_hor_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,3,'2026-06-26','09:00:00','22:00:00','ocupado','2026-06-25 09:00:18');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_horario_no_solapamiento

BEFORE INSERT ON horarios

FOR EACH ROW

BEGIN

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

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `area` varchar(80) NOT NULL DEFAULT '',
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'Álgebra y Geometría Analítica','Matemática',1,'2026-06-23 21:46:42'),(2,'Análisis Matemático I','Matemática',1,'2026-06-23 21:46:42'),(3,'Análisis Matemático II','Matemática',1,'2026-06-23 21:46:42'),(4,'Cálculo Numérico','Matemática',1,'2026-06-23 21:46:42'),(5,'Física I','Física',1,'2026-06-23 21:46:42'),(6,'Física II','Física',1,'2026-06-23 21:46:42'),(7,'Química General','Ciencias Básicas',1,'2026-06-23 21:46:42'),(8,'Programación I','Informática',1,'2026-06-23 21:46:42'),(9,'Programación II','Informática',1,'2026-06-23 21:46:42'),(10,'Algoritmos y Estructuras de Datos','Informática',1,'2026-06-23 21:46:42'),(11,'Base de Datos','Informática',1,'2026-06-23 21:46:42'),(12,'Redes de Computadoras','Informática',1,'2026-06-23 21:46:42'),(13,'Sistemas Operativos','Informática',1,'2026-06-23 21:46:42'),(14,'Ingeniería de Software','Informática',1,'2026-06-23 21:46:42'),(15,'Ciberseguridad','Informática',1,'2026-06-23 21:46:42'),(16,'Lógica y Estructuras Discretas','Matemática',1,'2026-06-23 21:46:42'),(17,'Estadística','Matemática',1,'2026-06-23 21:46:42'),(18,'Inglés Técnico','Idiomas',1,'2026-06-23 21:46:42'),(19,'Contabilidad','Administración',1,'2026-06-23 21:46:42'),(20,'Economía','Administración',1,'2026-06-23 21:46:42');
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alumno_id` int(10) unsigned NOT NULL,
  `tutor_id` int(10) unsigned NOT NULL,
  `horario_id` int(10) unsigned NOT NULL,
  `materia_id` int(10) unsigned NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('pendiente','aceptada','rechazada','cancelada','finalizada') NOT NULL DEFAULT 'pendiente',
  `cancelada_por` enum('alumno','tutor') DEFAULT NULL,
  `calificacion` tinyint(3) unsigned DEFAULT NULL COMMENT '1-5 estrellas, pone el alumno',
  `comentario_cal` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sol_alumno` (`alumno_id`),
  KEY `fk_sol_tutor` (`tutor_id`),
  KEY `fk_sol_horario` (`horario_id`),
  KEY `fk_sol_materia` (`materia_id`),
  CONSTRAINT `fk_sol_alumno` FOREIGN KEY (`alumno_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_sol_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`),
  CONSTRAINT `fk_sol_materia` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`),
  CONSTRAINT `fk_sol_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes`
--

LOCK TABLES `solicitudes` WRITE;
/*!40000 ALTER TABLE `solicitudes` DISABLE KEYS */;
INSERT INTO `solicitudes` VALUES (1,2,3,1,6,'Hola necesito ayuda con toda la materia','finalizada',NULL,NULL,NULL,'2026-06-25 09:01:11','2026-06-25 09:02:39');
/*!40000 ALTER TABLE `solicitudes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_solicitud_aceptada

AFTER UPDATE ON solicitudes

FOR EACH ROW

BEGIN

    IF NEW.estado = 'aceptada' AND OLD.estado = 'pendiente' THEN

        UPDATE horarios SET estado = 'ocupado' WHERE id = NEW.horario_id;

    END IF;



    -- Al cancelar o rechazar → liberar horario

    IF (NEW.estado IN ('cancelada','rechazada')) AND OLD.estado = 'aceptada' THEN

        UPDATE horarios SET estado = 'libre' WHERE id = NEW.horario_id;

    END IF;



    -- Al finalizar → dejar ocupado (ya pasó) pero registrar

    -- (el estado 'ocupado' permanece; el tutor puede borrar el slot si quiere)

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `solicitudes_materia`
--

DROP TABLE IF EXISTS `solicitudes_materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes_materia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `area` varchar(60) NOT NULL,
  `motivo` text DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `admin_nota` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `solicitudes_materia_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_materia`
--

LOCK TABLES `solicitudes_materia` WRITE;
/*!40000 ALTER TABLE `solicitudes_materia` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitudes_materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tutor_materias`
--

DROP TABLE IF EXISTS `tutor_materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutor_materias` (
  `tutor_id` int(10) unsigned NOT NULL,
  `materia_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tutor_id`,`materia_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `tutor_materias_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tutor_materias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tutor_materias`
--

LOCK TABLES `tutor_materias` WRITE;
/*!40000 ALTER TABLE `tutor_materias` DISABLE KEYS */;
INSERT INTO `tutor_materias` VALUES (3,5),(3,6),(3,7);
/*!40000 ALTER TABLE `tutor_materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tutor','alumno') NOT NULL DEFAULT 'alumno',
  `estado` enum('pendiente','activo','bloqueado') NOT NULL DEFAULT 'activo',
  `carrera` varchar(120) NOT NULL DEFAULT '',
  `anio_cursado` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `universidad` varchar(120) NOT NULL DEFAULT '',
  `certificado_path` varchar(255) DEFAULT NULL COMMENT 'ruta al archivo subido',
  `wallet_address` varchar(100) DEFAULT NULL COMMENT 'wallet simulada Ethereum',
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@skillswap.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','activo','',0,'',NULL,NULL,NULL,'2026-06-23 21:46:28','2026-06-23 21:46:28'),(2,'Lucila Montuori','jefe@talenthub.com','$2y$10$UDz6Q2PXaIns5v6I31n7wupBf.Fw3ULBXsxm0ACtgkNNk.tEGxmpi','alumno','activo','Ingenieria en Sistemas de Información',2,'Universidad Champagnat',NULL,NULL,'','2026-06-25 08:32:19','2026-06-25 08:32:19'),(3,'Marmot Hormigota','jefe1@talenthub.com','$2y$10$wzKYVUQEQU3UGhmrplpDP.S9sD4lHm8UZbcIvIBw/J8FNZPUAhsk.','tutor','activo','Bioquimica',4,'Universidad Nacional de Cuyo','uploads/certificados/cert_6a3d50530b38e2.09746175.png','','Hola! Mi nombre es Marmot y soy tutor especializado en Ciencias.\r\nSoy muy paciente y conozco muchas técnicas de estudio que pueden ayudar, además de manejar de manera excelente el contenido que enseño.','2026-06-25 15:59:16','2026-06-25 15:59:47');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'skillswap'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-25 13:44:23
