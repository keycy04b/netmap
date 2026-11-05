-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-11-2026 a las 18:38:42
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
-- Base de datos: `netmap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enlaces`
--

CREATE TABLE `enlaces` (
  `id_enlace` int(11) NOT NULL,
  `id_puerto_origen` int(11) NOT NULL,
  `id_puerto_destino` int(11) NOT NULL,
  `estilo_linea` enum('DISCONTINUA','CONTINUA','PERSONALIZADO') NOT NULL DEFAULT 'DISCONTINUA',
  `color_linea` varchar(20) NOT NULL DEFAULT '#FF0000',
  `caracteristicas` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `enlaces`
--

INSERT INTO `enlaces` (`id_enlace`, `id_puerto_origen`, `id_puerto_destino`, `estilo_linea`, `color_linea`, `caracteristicas`, `observaciones`, `fecha_creacion`) VALUES
(1, 445, 25, 'DISCONTINUA', '#E83EAA', NULL, 'Enlace creado desde el registro de equipos', '2026-10-22 15:25:20'),
(6, 591, 615, 'DISCONTINUA', '#E83EAA', NULL, 'Enlace creado desde el registro de equipos', '2026-10-22 16:02:23'),
(7, 639, 470, 'CONTINUA', '#2563EB', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:29:24'),
(8, 641, 466, 'DISCONTINUA', '#EF4444', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:36:13'),
(9, 649, 640, 'CONTINUA', '#2563EB', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:36:14'),
(10, 650, 469, 'CONTINUA', '#2563EB', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:36:14'),
(11, 644, 447, 'DISCONTINUA', '#EF4444', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:36:14'),
(12, 646, 462, 'DISCONTINUA', '#EF4444', NULL, 'Enlace creado desde el registro de equipos', '2026-10-27 18:36:14'),
(13, 643, 399, 'DISCONTINUA', '#EF4444', NULL, 'Enlace creado desde el editor de equipos', '2026-11-05 15:59:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `id_piso` int(11) NOT NULL,
  `id_tipo_equipo` int(11) NOT NULL,
  `icono` text DEFAULT NULL,
  `nombre_equipo` varchar(255) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `direccion_ip` varchar(45) DEFAULT NULL,
  `tipo_red` enum('LAN','WAN') NOT NULL DEFAULT 'LAN',
  `numero_puertos` int(11) NOT NULL DEFAULT 0,
  `posicion_x` decimal(10,2) DEFAULT 0.00,
  `posicion_y` decimal(10,2) DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id_equipo`, `id_piso`, `id_tipo_equipo`, `icono`, `nombre_equipo`, `marca`, `modelo`, `serial`, `direccion_ip`, `tipo_red`, `numero_puertos`, `posicion_x`, `posicion_y`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 8, 1, 'assets/icons/custom/1791999828_images.jpg', 'Swiche 1 Sede 4 Piso 7', '0', 'edas32', '8yd8b11c1tk000244', '342234343', 'LAN', 24, 150.00, 150.00, '', '2026-10-14 17:43:48', '2026-10-15 20:51:35'),
(3, 4, 2, NULL, 'Computadora 1 Sede 2 Piso 1', '0', '45ywru', 't457646y7w45y', 'ghstrhstrhys', 'LAN', 4, 100.00, 100.00, '', '2026-10-14 18:18:29', '2026-10-14 18:18:29'),
(4, 6, 3, NULL, 'ftydtjyu', '0', 'dtysrtysrty', 'srturtuhjsru', 'srturty5', 'LAN', 24, 100.00, 100.00, '', '2026-10-14 18:26:00', '2026-10-14 18:26:00'),
(5, 2, 2, 'fthsh', 'xdfghtrhj', '0', 'xdfhthtdrhtrdh', 'zxdthtrhft', 'xgfhtrhjurjm', 'LAN', 2, 100.00, 100.00, '', '2026-10-14 18:35:35', '2026-10-14 18:35:35'),
(6, 2, 4, 'ert4545y', '235', 'drgsey456', '456w456trg', 'ser4y7645y', 'se5y456', 'LAN', 25, 100.00, 100.00, '', '2026-10-14 19:08:37', '2026-10-14 19:08:37'),
(7, 1, 2, NULL, 'erhytehtrh', 'Et6etyety', 'Sgh45yw4hy', 'TY46THTRH', 'rthy45yr6th', 'LAN', 2, 100.00, 100.00, '', '2026-10-14 19:20:12', '2026-10-14 19:20:12'),
(8, 2, 3, NULL, 'eeeeeetetet', 'Eeeeeyrty', 'Eeeetytry', 'EEEEEETYERTYTYE', 'eeeeeertet', 'LAN', 2, 100.00, 100.00, '', '2026-10-14 19:23:15', '2026-10-14 19:23:15'),
(9, 4, 4, NULL, 'Zsdrgrgrsgrg', 'Srgdegdbgg', 'Ggfrdgfrdrgrseg', 'ZRGREGTERGERG', 'zdrgergtergerg', 'LAN', 2, 100.00, 100.00, '', '2026-10-14 19:25:50', '2026-10-14 19:25:50'),
(10, 1, 1, 'afafaef', 'Swiche 1 Sede Central 1', 'Efafaef', 'Afafaef', 'AEFEAFQEF', 'dfdfafaf', 'LAN', 2, 100.00, 100.00, '', '2026-10-14 19:30:55', '2026-10-14 19:30:55'),
(11, 1, 6, NULL, 'Kl Lkfvg Klfg', 'Fmjkdf', 'Sldfnsf', 'LOSDKFNMOSD', 'lks fmks f', 'LAN', 24, 0.00, 0.00, 'Dfg-ñdfg', '2026-10-14 21:13:57', '2026-10-14 21:13:57'),
(12, 6, 2, NULL, 'Computadora 1 Sede 3 Piso 1', 'Jhsvyudvyu', 'Kjsncnijbuio', 'UIEBDFJKDBCHJSDB', 'ksdncvjkdcjidb', 'LAN', 4, 0.00, 0.00, '', '2026-10-14 21:13:57', '2026-10-14 21:13:57'),
(13, 4, 2, NULL, 'Oiuh9u8', 'Kjui', 'Koioj', 'IOJI0J', 'iju9', 'LAN', 25, 0.00, 0.00, 'K9jk', '2026-10-14 22:14:17', '2026-10-14 22:14:17'),
(14, 7, 2, NULL, 'Fthr', 'DEWEF', 'Frgg', 'RGRDG', 'dfgdgde', 'LAN', 26, 0.00, 0.00, 'Fgh', '2026-10-15 15:51:20', '2026-10-15 15:51:20'),
(15, 1, 1, NULL, 'Swiche 2 Sede Central 1', 'Pueba', '1', '1', '1', 'LAN', 6, 0.00, 0.00, 'Raro', '2026-10-15 16:26:10', '2026-10-15 16:26:10'),
(16, 1, 1, NULL, 'Swiche 3 Sede Central 1', 'Pruuu', 'Ghgf', 'FGH', 'fghj', 'LAN', 2, 0.00, 0.00, '', '2026-10-15 18:30:07', '2026-10-15 18:30:07'),
(19, 1, 1, NULL, 'Swiche 4 Sede Central 1', 'rjusrhjxsr', 'afafaef', '21101', 'dfzd fzdf', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 13:46:29', '2026-10-21 13:46:29'),
(20, 4, 1, NULL, 'Swiche 1 Sede 2 Piso 1', 'wer', 'dger', '21102GFGFGFG', 'dfdfafaxdf', 'LAN', 20, 0.00, 0.00, '', '2026-10-21 13:46:29', '2026-10-21 13:46:29'),
(21, 1, 1, NULL, 'Swiche 5 Sede Central 1', 'cisco', 'afafaef', '8YD8B11C1TK000244', 'zfsdresr', 'LAN', 24, 0.00, 0.00, '211011', '2026-10-21 13:51:17', '2026-10-21 13:51:17'),
(22, 1, 2, NULL, '211012', 'cisco', 'kl', 'BHKJHK', 'jbhbhk', 'LAN', 16, 0.00, 0.00, '', '2026-10-21 13:51:17', '2026-10-21 13:51:17'),
(23, 1, 1, NULL, '11111111', 'csdvdfv', 'vfdfvdfbdf', 'DFBDFBDFBSDFB', 'dfbdbgdfbbf', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 14:57:06', '2026-10-21 14:57:06'),
(24, 1, 1, NULL, '2222', 'cisco', 'afafaef', 'G6G56Y346', '245t425t45t', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 15:00:08', '2026-10-21 15:00:08'),
(25, 1, 1, NULL, '333333', 'cisco', 'afafaef', 'TRHRHETYH', 'thetyhetyhtey', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 15:01:09', '2026-10-21 15:01:09'),
(26, 1, 1, NULL, 'pruuuu1', 'cisco', 'afafaef', 'WEQWRRQWER', 'weawerawerwer', 'LAN', 26, 0.00, 0.00, '', '2026-10-21 18:30:16', '2026-10-21 18:30:16'),
(27, 1, 2, NULL, 'editaree', 'efwefwe', 'wefrwefwe', 'WEFWEFWE', 'wefwefwefg', 'LAN', 4, 0.00, 0.00, '', '2026-10-21 18:30:16', '2026-10-23 18:25:16'),
(28, 2, 1, NULL, '1', 'rjusrhjxsr', 'edas32', 'SDSDVSD', 'sddvasdvasfv', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 18:36:14', '2026-10-21 18:36:14'),
(29, 1, 1, NULL, 'pruu enl', 'cisco', 'afafaef', 'SDGSG', 'xdgfsdgf', 'LAN', 24, 0.00, 0.00, '', '2026-10-21 19:38:51', '2026-10-21 19:38:51'),
(30, 4, 2, NULL, 'pru enl', 'efwefwe', 'sefwefqwfr', 'EFQEFQWEF', 'aefqwefqwef', 'LAN', 4, 0.00, 0.00, '', '2026-10-21 19:38:52', '2026-10-21 19:38:52'),
(31, 1, 2, NULL, '54565615', '3056151561', 'bhugvgvf', 'RESRSERS', 'ERSERSERS', 'LAN', 8, 0.00, 0.00, '', '2026-10-21 19:52:39', '2026-10-21 19:52:39'),
(32, 1, 1, 'assets/icons/custom/icon_d8df1ab5ac0c12ff25caf891.png', 'Swiche 11 Sede Central 1', 'et6etyety', 'dtysrtysrty', '44444', 'tththdh', 'LAN', 26, 0.00, 0.00, '', '2026-10-22 15:25:20', '2026-10-22 15:25:20'),
(40, 2, 1, NULL, 'ftydtjyu', 'rjusrhjxsr', 'zs', '7777', 'cfgfdgfr', 'LAN', 24, 0.00, 0.00, '', '2026-10-22 16:02:23', '2026-10-22 16:02:23'),
(41, 1, 2, NULL, 'ftydtjyu', 'et6etyety', '45ywru', '7788', 'fhyfghdfg', 'LAN', 24, 0.00, 0.00, '', '2026-10-22 16:02:23', '2026-10-22 16:02:23'),
(42, 2, 2, 'assets/icons/computer.svg', 'Sd<szde<', 'Et6etyety', 'Zsndzs', 'SZDNZSDZS', '8888', 'LAN', 2, 0.00, 0.00, '', '2026-10-27 18:29:24', '2026-10-27 18:29:24'),
(43, 5, 3, 'assets/icons/switch.svg', 'Njk-hj', 'Hj.cf', 'V.ghgcfhg', 'HVG.H,CFH.', '9999', 'LAN', 10, 0.00, 0.00, '', '2026-10-27 18:36:13', '2026-10-27 18:36:13');

--
-- Disparadores `equipos`
--
DELIMITER $$
CREATE TRIGGER `trg_equipos_nombre_autogenerado` BEFORE INSERT ON `equipos` FOR EACH ROW BEGIN
    DECLARE v_tipo VARCHAR(100);
    DECLARE v_sede VARCHAR(100);
    DECLARE v_piso VARCHAR(50);
    DECLARE v_correlativo INT;

    IF NEW.nombre_equipo IS NULL OR TRIM(NEW.nombre_equipo) = '' THEN
        SELECT nombre_tipo INTO v_tipo 
        FROM tipos_equipo 
        WHERE id_tipo_equipo = NEW.id_tipo_equipo;

        SELECT s.nombre_sede, p.numero_piso INTO v_sede, v_piso
        FROM pisos p
        INNER JOIN sedes s ON p.id_sede = s.id_sede
        WHERE p.id_piso = NEW.id_piso;

        SELECT COUNT(*) + 1 INTO v_correlativo
        FROM equipos
        WHERE id_piso = NEW.id_piso AND id_tipo_equipo = NEW.id_tipo_equipo;

        SET NEW.nombre_equipo = CONCAT(v_tipo, ' ', v_correlativo, ' ', v_sede, ' ', v_piso);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metricas_tiempo_real`
--

CREATE TABLE `metricas_tiempo_real` (
  `id_metrica` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `id_puerto` int(11) DEFAULT NULL,
  `estado_conexion` enum('ONLINE','OFFLINE','DEGRADADO') NOT NULL DEFAULT 'ONLINE',
  `latencia_ms` float DEFAULT NULL,
  `ancho_banda_uso` float DEFAULT NULL,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pisos`
--

CREATE TABLE `pisos` (
  `id_piso` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `numero_piso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pisos`
--

INSERT INTO `pisos` (`id_piso`, `id_sede`, `numero_piso`) VALUES
(1, 1, 'Piso 1'),
(2, 1, 'Piso 2'),
(3, 1, 'Piso 3'),
(4, 2, 'Piso 1'),
(5, 2, 'Piso 2'),
(6, 3, 'Piso 1'),
(7, 4, 'Piso 1'),
(8, 4, 'Piso 7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puertos`
--

CREATE TABLE `puertos` (
  `id_puerto` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `numero_puerto` int(11) NOT NULL,
  `etiqueta_puerto` varchar(50) NOT NULL DEFAULT '',
  `tipo_puerto` enum('RED','FIBRA') NOT NULL,
  `alcance` enum('LAN','WAN') NOT NULL DEFAULT 'LAN',
  `medio` enum('UTP','FIBRA') NOT NULL DEFAULT 'UTP',
  `grupo_puerto` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `secciones_fisicas` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('DISPONIBLE','NO_DISPONIBLE','RESERVADO','DANADO') NOT NULL DEFAULT 'DISPONIBLE',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puertos`
--

INSERT INTO `puertos` (`id_puerto`, `id_equipo`, `numero_puerto`, `etiqueta_puerto`, `tipo_puerto`, `alcance`, `medio`, `grupo_puerto`, `secciones_fisicas`, `estado`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(2, 1, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(3, 1, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(4, 1, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-27 14:31:27'),
(5, 1, 5, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(6, 1, 6, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(7, 1, 7, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(8, 1, 8, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(9, 1, 9, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(10, 1, 10, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(11, 1, 11, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(12, 1, 12, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(13, 1, 13, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(14, 1, 14, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(15, 1, 15, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-27 14:32:35'),
(16, 1, 16, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(17, 1, 17, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(18, 1, 18, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(19, 1, 19, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(20, 1, 20, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(21, 1, 21, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(22, 1, 22, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(23, 1, 23, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(24, 1, 24, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 13:43:48', '2026-10-15 16:51:35'),
(25, 3, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'NO_DISPONIBLE', NULL, '2026-10-14 14:18:29', '2026-10-14 14:18:29'),
(26, 3, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:18:29', '2026-10-14 14:18:29'),
(27, 3, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:18:29', '2026-10-14 14:18:29'),
(28, 3, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:18:29', '2026-10-14 14:18:29'),
(29, 4, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(30, 4, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(31, 4, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(32, 4, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(33, 4, 5, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(34, 4, 6, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(35, 4, 7, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(36, 4, 8, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(37, 4, 9, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(38, 4, 10, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(39, 4, 11, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(40, 4, 12, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(41, 4, 13, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(42, 4, 14, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(43, 4, 15, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(44, 4, 16, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(45, 4, 17, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(46, 4, 18, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(47, 4, 19, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(48, 4, 20, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(49, 4, 21, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(50, 4, 22, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(51, 4, 23, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(52, 4, 24, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:26:00', '2026-10-14 14:26:00'),
(53, 5, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:35:35', '2026-10-14 14:35:35'),
(54, 5, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 14:35:35', '2026-10-14 14:35:35'),
(55, 6, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(56, 6, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'NO_DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-11-03 13:11:48'),
(57, 6, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(58, 6, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(59, 6, 5, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(60, 6, 6, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(61, 6, 7, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(62, 6, 8, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(63, 6, 9, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(64, 6, 10, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(65, 6, 11, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(66, 6, 12, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(67, 6, 13, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(68, 6, 14, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(69, 6, 15, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(70, 6, 16, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(71, 6, 17, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(72, 6, 18, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(73, 6, 19, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(74, 6, 20, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(75, 6, 21, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(76, 6, 22, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(77, 6, 23, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(78, 6, 24, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(79, 6, 25, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:08:37', '2026-10-14 15:08:37'),
(80, 7, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:20:12', '2026-10-14 15:20:12'),
(81, 7, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:20:12', '2026-10-14 15:20:12'),
(82, 8, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:23:15', '2026-10-14 15:23:15'),
(83, 8, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:23:15', '2026-10-14 15:23:15'),
(84, 9, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:25:50', '2026-10-14 15:25:50'),
(85, 9, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:25:50', '2026-10-14 15:25:50'),
(86, 10, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:30:55', '2026-10-14 15:30:55'),
(87, 10, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 15:30:55', '2026-10-14 15:30:55'),
(88, 11, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(89, 11, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(90, 11, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(91, 11, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(92, 11, 5, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(93, 11, 6, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(94, 11, 7, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(95, 11, 8, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(96, 11, 9, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(97, 11, 10, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(98, 11, 11, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(99, 11, 12, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(100, 11, 13, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(101, 11, 14, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(102, 11, 15, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(103, 11, 16, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(104, 11, 17, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(105, 11, 18, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(106, 11, 19, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(107, 11, 20, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(108, 11, 21, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(109, 11, 22, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(110, 11, 23, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(111, 11, 24, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(112, 12, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(113, 12, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(114, 12, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(115, 12, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 17:13:57', '2026-10-14 17:13:57'),
(116, 13, 1, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(117, 13, 2, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(118, 13, 3, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(119, 13, 4, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(120, 13, 5, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(121, 13, 6, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(122, 13, 7, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(123, 13, 8, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(124, 13, 9, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(125, 13, 10, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(126, 13, 11, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(127, 13, 12, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(128, 13, 13, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(129, 13, 14, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(130, 13, 15, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(131, 13, 16, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(132, 13, 17, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(133, 13, 18, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(134, 13, 19, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(135, 13, 20, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(136, 13, 21, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(137, 13, 22, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(138, 13, 23, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(139, 13, 24, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(140, 13, 25, '', 'RED', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-14 18:14:17', '2026-10-14 18:14:17'),
(141, 14, 1, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(142, 14, 2, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(143, 14, 3, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(144, 14, 4, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(145, 14, 5, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(146, 14, 6, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(147, 14, 7, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(148, 14, 8, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(149, 14, 9, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(150, 14, 10, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(151, 14, 11, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(152, 14, 12, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(153, 14, 13, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(154, 14, 14, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(155, 14, 15, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(156, 14, 16, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(157, 14, 17, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(158, 14, 18, '', '', 'LAN', 'UTP', 1, 1, 'NO_DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(159, 14, 19, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(160, 14, 20, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(161, 14, 21, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(162, 14, 22, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(163, 14, 23, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(164, 14, 24, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(165, 14, 25, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(166, 14, 26, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 11:51:20', '2026-10-15 11:51:20'),
(167, 15, 1, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(168, 15, 2, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(169, 15, 3, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(170, 15, 4, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(171, 15, 5, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(172, 15, 6, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 12:26:10', '2026-10-15 12:26:10'),
(173, 16, 1, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 14:30:07', '2026-10-15 14:30:07'),
(174, 16, 2, '', '', 'LAN', 'UTP', 1, 1, 'DISPONIBLE', NULL, '2026-10-15 14:30:07', '2026-10-15 14:30:07'),
(199, 19, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(200, 19, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(201, 19, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(202, 19, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(203, 19, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(204, 19, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(205, 19, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(206, 19, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(207, 19, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(208, 19, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(209, 19, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(210, 19, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(211, 19, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(212, 19, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(213, 19, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(214, 19, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(215, 19, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(216, 19, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(217, 19, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(218, 19, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(219, 19, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(220, 19, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(221, 19, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(222, 19, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(223, 20, 1, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(224, 20, 2, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(225, 20, 3, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(226, 20, 4, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(227, 20, 5, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(228, 20, 6, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(229, 20, 7, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(230, 20, 8, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(231, 20, 9, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(232, 20, 10, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(233, 20, 11, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(234, 20, 12, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(235, 20, 13, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(236, 20, 14, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(237, 20, 15, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(238, 20, 16, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(239, 20, 17, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(240, 20, 18, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(241, 20, 19, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(242, 20, 20, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:46:29', '2026-10-21 09:46:29'),
(243, 21, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(244, 21, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(245, 21, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(246, 21, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(247, 21, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(248, 21, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(249, 21, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(250, 21, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(251, 21, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(252, 21, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(253, 21, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(254, 21, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(255, 21, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(256, 21, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(257, 21, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(258, 21, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(259, 21, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(260, 21, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(261, 21, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(262, 21, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(263, 21, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(264, 21, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(265, 21, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(266, 21, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(267, 22, 1, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(268, 22, 2, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(269, 22, 3, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(270, 22, 4, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(271, 22, 5, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(272, 22, 6, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(273, 22, 7, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(274, 22, 8, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(275, 22, 9, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(276, 22, 10, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(277, 22, 11, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(278, 22, 12, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(279, 22, 13, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(280, 22, 14, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(281, 22, 15, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(282, 22, 16, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 09:51:17', '2026-10-21 09:51:17'),
(283, 23, 1, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(284, 23, 2, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(285, 23, 3, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(286, 23, 4, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(287, 23, 5, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(288, 23, 6, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(289, 23, 7, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(290, 23, 8, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(291, 23, 9, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(292, 23, 10, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(293, 23, 11, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(294, 23, 12, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(295, 23, 13, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(296, 23, 14, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(297, 23, 15, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(298, 23, 16, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(299, 23, 17, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(300, 23, 18, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(301, 23, 19, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(302, 23, 20, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(303, 23, 21, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(304, 23, 22, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(305, 23, 23, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(306, 23, 24, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 10:57:06', '2026-10-21 10:57:06'),
(307, 24, 1, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(308, 24, 2, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(309, 24, 3, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(310, 24, 4, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(311, 24, 5, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(312, 24, 6, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(313, 24, 7, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(314, 24, 8, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(315, 24, 9, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(316, 24, 10, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(317, 24, 11, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(318, 24, 12, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(319, 24, 13, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(320, 24, 14, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(321, 24, 15, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(322, 24, 16, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(323, 24, 17, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(324, 24, 18, '', 'RED', 'WAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(325, 24, 19, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(326, 24, 20, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(327, 24, 21, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(328, 24, 22, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(329, 24, 23, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(330, 24, 24, '', 'RED', 'WAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:00:08', '2026-10-21 11:00:08'),
(331, 25, 1, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(332, 25, 2, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(333, 25, 3, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(334, 25, 4, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(335, 25, 5, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(336, 25, 6, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(337, 25, 7, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(338, 25, 8, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(339, 25, 9, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(340, 25, 10, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(341, 25, 11, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(342, 25, 12, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(343, 25, 13, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(344, 25, 14, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(345, 25, 15, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(346, 25, 16, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(347, 25, 17, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(348, 25, 18, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(349, 25, 19, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(350, 25, 20, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(351, 25, 21, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(352, 25, 22, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(353, 25, 23, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(354, 25, 24, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 11:01:09', '2026-10-21 11:01:09'),
(355, 26, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(356, 26, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(357, 26, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(358, 26, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(359, 26, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(360, 26, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(361, 26, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(362, 26, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(363, 26, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(364, 26, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(365, 26, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(366, 26, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(367, 26, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(368, 26, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(369, 26, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(370, 26, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(371, 26, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(372, 26, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(373, 26, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(374, 26, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(375, 26, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(376, 26, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(377, 26, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(378, 26, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(379, 26, 25, '', 'FIBRA', 'WAN', 'FIBRA', 2, 1, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(380, 26, 26, '', 'FIBRA', 'WAN', 'FIBRA', 2, 1, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(381, 27, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(382, 27, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(383, 27, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(384, 27, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:30:16', '2026-10-21 14:30:16'),
(385, 28, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(386, 28, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(387, 28, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(388, 28, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(389, 28, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(390, 28, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(391, 28, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(392, 28, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(393, 28, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(394, 28, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(395, 28, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(396, 28, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(397, 28, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(398, 28, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(399, 28, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-11-05 11:59:43'),
(400, 28, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(401, 28, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(402, 28, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(403, 28, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(404, 28, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(405, 28, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(406, 28, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(407, 28, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(408, 28, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 14:36:14', '2026-10-21 14:36:14'),
(409, 29, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(410, 29, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(411, 29, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(412, 29, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(413, 29, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(414, 29, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(415, 29, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(416, 29, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(417, 29, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(418, 29, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(419, 29, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(420, 29, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(421, 29, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(422, 29, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(423, 29, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(424, 29, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(425, 29, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(426, 29, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(427, 29, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(428, 29, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(429, 29, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(430, 29, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(431, 29, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(432, 29, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:51', '2026-10-21 15:38:51'),
(433, 30, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:52', '2026-10-21 15:38:52'),
(434, 30, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:52', '2026-10-21 15:38:52'),
(435, 30, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:52', '2026-10-21 15:38:52'),
(436, 30, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:38:52', '2026-10-21 15:38:52'),
(437, 31, 1, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(438, 31, 2, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(439, 31, 3, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(440, 31, 4, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(441, 31, 5, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(442, 31, 6, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(443, 31, 7, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(444, 31, 8, '', 'FIBRA', 'WAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-21 15:52:39', '2026-10-21 15:52:39'),
(445, 32, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(446, 32, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'RESERVADO', NULL, '2026-10-22 11:25:20', '2026-10-27 14:31:29'),
(447, 32, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-27 14:36:14'),
(448, 32, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(449, 32, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(450, 32, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(451, 32, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(452, 32, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(453, 32, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(454, 32, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(455, 32, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DANADO', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(456, 32, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(457, 32, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(458, 32, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(459, 32, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(460, 32, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(461, 32, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(462, 32, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-27 14:36:14'),
(463, 32, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'RESERVADO', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(464, 32, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(465, 32, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(466, 32, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-27 14:36:13'),
(467, 32, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(468, 32, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-22 11:25:20'),
(469, 32, 25, '', 'FIBRA', 'LAN', 'FIBRA', 2, 1, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-27 14:36:14'),
(470, 32, 26, '', 'FIBRA', 'LAN', 'FIBRA', 2, 1, 'NO_DISPONIBLE', NULL, '2026-10-22 11:25:20', '2026-10-27 14:29:24'),
(591, 40, 1, '', 'RED', 'LAN', 'UTP', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(592, 40, 2, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(593, 40, 3, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(594, 40, 4, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(595, 40, 5, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(596, 40, 6, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(597, 40, 7, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(598, 40, 8, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23');
INSERT INTO `puertos` (`id_puerto`, `id_equipo`, `numero_puerto`, `etiqueta_puerto`, `tipo_puerto`, `alcance`, `medio`, `grupo_puerto`, `secciones_fisicas`, `estado`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(599, 40, 9, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(600, 40, 10, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(601, 40, 11, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(602, 40, 12, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(603, 40, 13, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(604, 40, 14, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(605, 40, 15, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(606, 40, 16, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(607, 40, 17, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(608, 40, 18, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(609, 40, 19, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(610, 40, 20, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(611, 40, 21, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(612, 40, 22, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(613, 40, 23, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(614, 40, 24, '', 'RED', 'LAN', 'UTP', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(615, 41, 1, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(616, 41, 2, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(617, 41, 3, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(618, 41, 4, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(619, 41, 5, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(620, 41, 6, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(621, 41, 7, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(622, 41, 8, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(623, 41, 9, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(624, 41, 10, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(625, 41, 11, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(626, 41, 12, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(627, 41, 13, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(628, 41, 14, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(629, 41, 15, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(630, 41, 16, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(631, 41, 17, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(632, 41, 18, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(633, 41, 19, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(634, 41, 20, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(635, 41, 21, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(636, 41, 22, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(637, 41, 23, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(638, 41, 24, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'DISPONIBLE', NULL, '2026-10-22 12:02:23', '2026-10-22 12:02:23'),
(639, 42, 1, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-27 14:29:24', '2026-10-27 14:29:24'),
(640, 42, 2, '', 'FIBRA', 'LAN', 'FIBRA', 1, 2, 'NO_DISPONIBLE', NULL, '2026-10-27 14:29:24', '2026-10-27 14:36:14'),
(641, 43, 1, '', 'RED', 'LAN', 'UTP', 1, 4, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:13'),
(642, 43, 2, '', 'RED', 'LAN', 'UTP', 1, 4, 'DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:13'),
(643, 43, 3, '', 'RED', 'LAN', 'UTP', 1, 4, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-11-05 11:59:43'),
(644, 43, 4, '', 'RED', 'LAN', 'UTP', 1, 4, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:14'),
(645, 43, 5, '', 'RED', 'LAN', 'UTP', 1, 4, 'DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:13'),
(646, 43, 6, '', 'RED', 'LAN', 'UTP', 1, 4, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:14'),
(647, 43, 7, '', 'RED', 'LAN', 'UTP', 1, 4, 'DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:13'),
(648, 43, 8, '', 'RED', 'LAN', 'UTP', 1, 4, 'DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:13'),
(649, 43, 9, '', 'FIBRA', 'LAN', 'FIBRA', 2, 1, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:14'),
(650, 43, 10, '', 'FIBRA', 'LAN', 'FIBRA', 2, 1, 'NO_DISPONIBLE', NULL, '2026-10-27 14:36:13', '2026-10-27 14:36:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_auditoria`
--

CREATE TABLE `registro_auditoria` (
  `id_log` int(11) NOT NULL,
  `tipo_accion` enum('INSERCION','MODIFICACION','ELIMINACION') NOT NULL,
  `entidad_afectada` enum('EQUIPO','PUERTO','ENLACE','SEDE','PISO') NOT NULL,
  `id_registro_afectado` int(11) NOT NULL,
  `descripcion_cambio` text NOT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id_sede` int(11) NOT NULL,
  `nombre_sede` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id_sede`, `nombre_sede`, `descripcion`) VALUES
(1, 'Sede Central', 'Edificio principal de administración'),
(2, 'Sede 2', 'Edificio de operaciones'),
(3, 'Sede 3', 'Sucursal norte'),
(4, 'Sede 4', 'Anexo de tecnología e ingeniería');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias_puertos`
--

CREATE TABLE `sugerencias_puertos` (
  `id_sugerencia` int(10) UNSIGNED NOT NULL,
  `id_tipo_equipo` int(11) NOT NULL,
  `alcance` enum('LAN','WAN') NOT NULL DEFAULT 'LAN',
  `medio` enum('UTP','FIBRA') NOT NULL DEFAULT 'UTP',
  `cantidad` smallint(5) UNSIGNED NOT NULL,
  `secciones` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sugerencias_puertos`
--

INSERT INTO `sugerencias_puertos` (`id_sugerencia`, `id_tipo_equipo`, `alcance`, `medio`, `cantidad`, `secciones`, `activo`) VALUES
(1, 1, 'LAN', 'UTP', 8, 1, 1),
(2, 1, 'LAN', 'UTP', 16, 2, 1),
(3, 1, 'LAN', 'UTP', 24, 2, 1),
(4, 1, 'LAN', 'UTP', 48, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_equipo`
--

CREATE TABLE `tipos_equipo` (
  `id_tipo_equipo` int(11) NOT NULL,
  `nombre_tipo` varchar(100) NOT NULL,
  `ruta_icono` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_equipo`
--

INSERT INTO `tipos_equipo` (`id_tipo_equipo`, `nombre_tipo`, `ruta_icono`) VALUES
(1, 'Swiche', 'assets/icons/switch.png'),
(2, 'Computadora', 'assets/icons/pc.png'),
(3, 'Router', 'assets/icons/router.png'),
(4, 'Servidor', 'assets/icons/server.png'),
(5, 'Equipo de Transporte', 'assets/icons/transport.png'),
(6, 'Teléfono IP', 'assets/icons/phone.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `enlaces`
--
ALTER TABLE `enlaces`
  ADD PRIMARY KEY (`id_enlace`),
  ADD KEY `fk_enlaces_puerto_origen` (`id_puerto_origen`),
  ADD KEY `fk_enlaces_puerto_destino` (`id_puerto_destino`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD UNIQUE KEY `direccion_ip` (`direccion_ip`),
  ADD KEY `fk_equipos_pisos` (`id_piso`),
  ADD KEY `fk_equipos_tipos` (`id_tipo_equipo`);

--
-- Indices de la tabla `metricas_tiempo_real`
--
ALTER TABLE `metricas_tiempo_real`
  ADD PRIMARY KEY (`id_metrica`),
  ADD KEY `fk_metricas_equipos` (`id_equipo`),
  ADD KEY `fk_metricas_puertos` (`id_puerto`);

--
-- Indices de la tabla `pisos`
--
ALTER TABLE `pisos`
  ADD PRIMARY KEY (`id_piso`),
  ADD KEY `fk_pisos_sedes` (`id_sede`);

--
-- Indices de la tabla `puertos`
--
ALTER TABLE `puertos`
  ADD PRIMARY KEY (`id_puerto`),
  ADD KEY `fk_puertos_equipos` (`id_equipo`);

--
-- Indices de la tabla `registro_auditoria`
--
ALTER TABLE `registro_auditoria`
  ADD PRIMARY KEY (`id_log`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id_sede`);

--
-- Indices de la tabla `sugerencias_puertos`
--
ALTER TABLE `sugerencias_puertos`
  ADD PRIMARY KEY (`id_sugerencia`),
  ADD UNIQUE KEY `uq_sugerencia_tipo` (`id_tipo_equipo`,`alcance`,`medio`,`cantidad`),
  ADD KEY `idx_sugerencia_tipo_activo` (`id_tipo_equipo`,`activo`);

--
-- Indices de la tabla `tipos_equipo`
--
ALTER TABLE `tipos_equipo`
  ADD PRIMARY KEY (`id_tipo_equipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `enlaces`
--
ALTER TABLE `enlaces`
  MODIFY `id_enlace` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `metricas_tiempo_real`
--
ALTER TABLE `metricas_tiempo_real`
  MODIFY `id_metrica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pisos`
--
ALTER TABLE `pisos`
  MODIFY `id_piso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `puertos`
--
ALTER TABLE `puertos`
  MODIFY `id_puerto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651;

--
-- AUTO_INCREMENT de la tabla `registro_auditoria`
--
ALTER TABLE `registro_auditoria`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id_sede` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sugerencias_puertos`
--
ALTER TABLE `sugerencias_puertos`
  MODIFY `id_sugerencia` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_equipo`
--
ALTER TABLE `tipos_equipo`
  MODIFY `id_tipo_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `enlaces`
--
ALTER TABLE `enlaces`
  ADD CONSTRAINT `fk_enlaces_puerto_destino` FOREIGN KEY (`id_puerto_destino`) REFERENCES `puertos` (`id_puerto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enlaces_puerto_origen` FOREIGN KEY (`id_puerto_origen`) REFERENCES `puertos` (`id_puerto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipos_pisos` FOREIGN KEY (`id_piso`) REFERENCES `pisos` (`id_piso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_equipos_tipos` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipos_equipo` (`id_tipo_equipo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `metricas_tiempo_real`
--
ALTER TABLE `metricas_tiempo_real`
  ADD CONSTRAINT `fk_metricas_equipos` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_metricas_puertos` FOREIGN KEY (`id_puerto`) REFERENCES `puertos` (`id_puerto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pisos`
--
ALTER TABLE `pisos`
  ADD CONSTRAINT `fk_pisos_sedes` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `puertos`
--
ALTER TABLE `puertos`
  ADD CONSTRAINT `fk_puertos_equipos` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sugerencias_puertos`
--
ALTER TABLE `sugerencias_puertos`
  ADD CONSTRAINT `fk_sugerencias_tipo` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipos_equipo` (`id_tipo_equipo`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
