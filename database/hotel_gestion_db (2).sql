-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2025 a las 22:38:13
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
-- Base de datos: `hotel_gestion_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `documento_identidad` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL COMMENT 'País o ciudad de origen del cliente.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `documento_identidad`, `nombre`, `telefono`, `origen`) VALUES
(1, '18.358.698-K', 'SUSANA ALEJANDRA ESTANCA GUZMÁN', NULL, 'CHILE'),
(2, '21542035', 'PABLO GABRIEL GUTIÉRREZ LUNA', NULL, 'PERÚ'),
(3, '25709325', 'LUIS ALBERTO VILLEGAS BENITES', NULL, 'PERÚ'),
(4, '29535515', 'JIMMY MIGUEL OJEDA MORALES', NULL, 'PERU'),
(5, '5.675.709-o', 'JUAN BELARMINO CÁRDENAS VELÁSQUEZ', NULL, 'CHILE'),
(6, '8805819', 'JOHN FRANKY ZORRILLA NOCE', NULL, 'PERU'),
(7, '47181160', 'ROSEL VILLENA GUEVARA', NULL, 'PERÚ'),
(8, '12.330.116-3', 'CLAUDIO ENRIQUE FLORES LETTON', NULL, 'CHILE'),
(9, '18.966.119-3', 'ÁLVARO ALEJANDRO PAREDES', NULL, 'CHILE'),
(10, '18114077', 'ARMANDO BENJAMÍN DE LA ROSA CELESTINO', NULL, 'PERÚ'),
(11, '5410569', 'GIOVANNA STEFANÍA PINASCO TORRES', NULL, 'PERÚ'),
(12, '8.172.691-4', 'RIGOBERTO ALONSO GONZÁLEZ MUÑOZ', NULL, 'CHILE'),
(13, '03470358-8', 'CARLOS RAÚL CASTILLO ROSAS', NULL, 'PERU'),
(14, '5.399.363-K', 'MARÍA NIEVES VERA GARAY', NULL, 'CHILE'),
(15, '71014530', 'CARLOS RAÚL ALPASTA VILLA GÓMEZ', NULL, 'PERÚ'),
(16, '11.137.845-2', 'MARÍA JEANETTE JIMÉNEZ RIVERA', NULL, 'CHILE'),
(17, '9.286.340-9', 'EDITH GENDERY LÓPEZ AGUIRRE', NULL, 'CHILE'),
(18, '15762765', 'LOURDES ALISON CHUMBES DIAZ', NULL, 'PERÚ'),
(19, '14.333.886-K', 'FRANKLIN PATRICIO RIVAS TOLEDO', NULL, 'CHILE'),
(20, '72517739', 'LUIS ANGEL QUIÑONEZ RODRIGUEZ', NULL, 'PERÚ'),
(32, '29707066', 'LUZ RODRÍGUEZ', NULL, 'PERU'),
(33, 'PAR665485', 'IÑAKI ARREGUI BRAVO', NULL, 'ESPAÑA'),
(34, '17.444.057-3', 'FRANCISCO ANTONIO CANALES ORTIZ', NULL, 'CHILE'),
(35, '71785134', 'ROMEL MARCIAL ROSALES HUERTA', NULL, 'PERÚ'),
(36, '41550643', 'SANTIAGO MIGUEL ROMASCONO ESPINOZA', NULL, 'PERÚ'),
(37, '42822796', 'JOSÉ LUIS DOLORES MACEDO', NULL, 'PERÚ'),
(38, '8041967', 'ELADIA QUISPE YARICO', NULL, 'PERÚ'),
(39, '40662254', 'HARRY ALBERTO HONORIO MORALES', NULL, 'PERÚ'),
(40, '29520863', 'MARÍA GLADIS ELENA REDOYA NEZALCAGA', NULL, 'PERÚ'),
(41, '29470998', 'CLORIS JORGE FERNÁNDEZ VELAZCO', NULL, 'PERÚ'),
(42, '10729958', 'CARLOS AUGUSTO JIMÉNEZ TEÑAN', NULL, 'PERÚ'),
(43, '8238074', 'NELI MERCEDES SANTILLÁN MEZA', NULL, 'PERÚ'),
(46, '8668770', 'PERCY ERNESTO SUÁREZ NIÑO', NULL, 'PERÚ'),
(47, '29483253', 'EDWIN JONATHAN LORENZO ESPINNA VILLORANIA', NULL, 'PERÚ'),
(48, '41599634', 'FREDDY TICONA ESTEBAN', NULL, 'PERÚ'),
(49, '4431757', 'FERNANDO ALONSO OCHOA PACHECO', NULL, 'PERÚ'),
(50, '70356029', 'VICTOR RAÚL CUTIPA CCAMA', NULL, 'PERÚ'),
(55, '4647055', 'HAMERTO GETULIO MENSING SANGUINETTI', NULL, 'CHILE'),
(56, '6.258.323-1', 'JORGE ALFONSO ARAMBUR CASTRO', NULL, 'CHILE'),
(57, '15.980.152-7', 'YERLY MARETH GAYTAN VEGA', NULL, 'CHILE'),
(58, '3667122', 'CARLOMAGNO DAGOBERTO AVELLANEDA VALDIVIESO', NULL, 'PERÚ'),
(59, '12.128.128-7', 'PEDRO ANÍBAL ESCOBAR SOTO', NULL, 'CHILE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `numero_habitacion` varchar(10) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `especificacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habitaciones`
--

INSERT INTO `habitaciones` (`numero_habitacion`, `tipo_id`, `precio`, `especificacion`) VALUES
('102', 2, 130.00, 'Cama Queen'),
('103', 1, 75.00, NULL),
('104', 4, 160.00, '2 camas de 2 plazas'),
('105', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('201', 2, 130.00, 'Cama Queen'),
('202', 2, 130.00, 'Cama Queen'),
('203', 2, 130.00, 'Cama Queen'),
('204', 1, 75.00, NULL),
('205', 4, 160.00, '2 camas de 2 plazas'),
('206', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('207', 2, 110.00, 'Cama Queen'),
('208', 2, 110.00, 'Cama Queen'),
('209', 2, 110.00, 'Puede ser usada como Simple'),
('210', 2, 110.00, 'Cama Queen'),
('211', 5, 110.00, '2 camas de 1 plaza y media'),
('212', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('213', 2, 110.00, 'Cama Queen'),
('301', 2, 130.00, 'Cama Queen'),
('302', 2, 130.00, 'Cama Queen'),
('303', 2, 130.00, 'Cama Queen'),
('304', 1, 75.00, NULL),
('305', 4, 160.00, '2 camas de 2 plazas'),
('306', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('307', 2, 110.00, 'Cama Queen'),
('308', 2, 110.00, 'Cama Queen'),
('309', 2, 110.00, 'Puede ser usada como Simple'),
('310', 2, 110.00, 'Cama Queen'),
('311', 5, 110.00, '2 camas de 1 plaza y media'),
('312', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('313', 2, 110.00, 'Cama Queen'),
('401', 2, 130.00, 'Cama Queen'),
('402', 2, 130.00, 'Cama Queen'),
('403', 2, 130.00, 'Cama Queen'),
('404', 1, 75.00, NULL),
('405', 4, 160.00, '2 camas de 2 plazas'),
('406', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('407', 2, 110.00, 'Cama Queen'),
('408', 2, 110.00, 'Cama Queen'),
('409', 2, 110.00, 'Puede ser usada como Simple'),
('410', 2, 110.00, 'Cama Queen'),
('411', 5, 110.00, '2 camas de 1 plaza y media'),
('412', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('413', 2, 110.00, 'Cama Queen'),
('501', 3, 160.00, '3 camas de 1 plaza y media'),
('502', 2, 130.00, 'Cama Queen'),
('503', 2, 130.00, 'Cama Queen'),
('504', 1, 75.00, NULL),
('505', 4, 160.00, '2 camas de 2 plazas'),
('506', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('507', 2, 110.00, 'Cama Queen'),
('508', 2, 110.00, 'Cama Queen'),
('509', 2, 110.00, 'Puede ser usada como Simple'),
('510', 2, 110.00, 'Cama Queen'),
('511', 5, 110.00, '2 camas de 1 plaza y media'),
('512', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('513', 2, 110.00, 'Cama Queen'),
('601', 4, 180.00, '1 cama de 2 plazas y 2 de 1 plaza y media'),
('602', 4, 180.00, '1 cama de 2 plazas y 2 de 1 plaza y media'),
('603', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media'),
('604', 4, 160.00, '2 camas de 2 plazas'),
('605', 3, 140.00, '1 cama matrimonial + 1 de 1 plaza y media');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_stock`
--

CREATE TABLE `historial_stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `cambio` int(11) NOT NULL COMMENT 'Diferencia entre nuevo y anterior (puede ser positivo o negativo)',
  `motivo` varchar(255) NOT NULL COMMENT 'Ej: "Venta a Hab. 201", "Ajuste manual", "Compra a proveedor"',
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_stock`
--

INSERT INTO `historial_stock` (`id`, `producto_id`, `stock_anterior`, `stock_nuevo`, `cambio`, `motivo`, `fecha_cambio`) VALUES
(1, 1, 3, 2, -1, 'Venta a Hab. 407', '2025-06-22 13:48:06'),
(2, 3, 7, 6, -1, 'Venta a Hab. 407', '2025-06-22 13:48:06'),
(3, 10, 3, 2, -1, 'Venta a Hab. 209', '2025-06-22 17:35:06'),
(4, 11, 12, 11, -1, 'Venta a Hab. 209', '2025-06-22 17:35:06'),
(5, 8, 5, 4, -1, 'Venta a Hab. 209', '2025-06-22 17:35:06'),
(6, 12, 26, 25, -1, 'Venta a Hab. 603', '2025-06-22 17:19:56'),
(7, 13, 8, 7, -1, 'Venta a Hab. 603', '2025-06-22 17:19:56'),
(8, 10, 2, 1, -1, 'Venta a Hab. 603', '2025-06-22 14:28:22'),
(9, 4, 7, 6, -1, 'Venta a Hab. 603', '2025-06-22 21:38:21'),
(10, 5, 14, 13, -1, 'Venta a Hab. 407', '2025-06-23 20:22:21'),
(11, 13, 7, 6, -1, 'Venta a Hab. 407', '2025-06-23 20:22:21'),
(12, 3, 6, 5, -1, 'Venta a Hab. 407', '2025-06-23 20:22:21'),
(13, 9, 12, 11, -1, 'Venta a Hab. 407', '2025-06-23 10:02:03'),
(14, 4, 6, 5, -1, 'Venta a Hab. 407', '2025-06-23 10:02:03'),
(15, 8, 4, 3, -1, 'Venta a Hab. 407', '2025-06-23 10:02:03'),
(16, 13, 6, 5, -1, 'Venta a Hab. 603', '2025-06-23 14:13:00'),
(17, 12, 25, 24, -1, 'Venta a Hab. 603', '2025-06-23 14:13:00'),
(18, 9, 11, 10, -1, 'Venta a Hab. 603', '2025-06-23 14:13:00'),
(19, 2, 1, 0, -1, 'Venta a Hab. 403', '2025-06-24 18:19:35'),
(20, 3, 5, 4, -1, 'Venta a Hab. 210', '2025-06-24 21:43:49'),
(21, 4, 5, 4, -1, 'Venta a Hab. 210', '2025-06-24 21:43:49'),
(22, 8, 3, 2, -1, 'Venta a Hab. 410', '2025-06-24 14:13:21'),
(23, 11, 11, 10, -1, 'Venta a Hab. 410', '2025-06-24 14:13:21'),
(24, 1, 2, 1, -1, 'Venta a Hab. 410', '2025-06-24 14:13:21'),
(25, 1, 1, 0, -1, 'Venta a Hab. 310', '2025-06-25 20:14:34'),
(26, 3, 4, 3, -1, 'Venta a Hab. 310', '2025-06-25 20:14:34'),
(27, 7, 10, 9, -1, 'Venta a Hab. 310', '2025-06-25 20:14:34'),
(28, 7, 9, 8, -1, 'Venta a Hab. 405', '2025-06-25 18:01:08'),
(29, 9, 10, 9, -1, 'Venta a Hab. 405', '2025-06-25 18:01:08'),
(30, 7, 8, 7, -1, 'Venta a Hab. 405', '2025-06-25 18:01:08'),
(31, 10, 1, 0, -1, 'Venta a Hab. 405', '2025-06-25 14:30:11'),
(32, 3, 3, 2, -1, 'Venta a Hab. 405', '2025-06-25 14:30:11'),
(33, 12, 24, 23, -1, 'Venta a Hab. 103', '2025-06-25 13:42:18'),
(34, 13, 5, 4, -1, 'Venta a Hab. 103', '2025-06-25 13:42:18'),
(35, 11, 10, 9, -1, 'Venta a Hab. 103', '2025-06-25 13:42:18'),
(36, 4, 4, 3, -1, 'Venta a Hab. 213', '2025-06-25 13:59:44'),
(37, 7, 7, 6, -1, 'Venta a Hab. 213', '2025-06-25 13:59:44'),
(38, 8, 2, 1, -1, 'Venta a Hab. 104', '2025-06-26 15:17:06'),
(39, 12, 23, 22, -1, 'Venta a Hab. 104', '2025-06-26 15:17:06'),
(40, 8, 1, 0, -1, 'Venta a Hab. 104', '2025-06-26 15:17:06'),
(41, 3, 2, 1, -1, 'Venta a Hab. 304', '2025-06-26 15:57:22'),
(42, 3, 1, 0, -1, 'Venta a Hab. 304', '2025-06-26 15:57:22'),
(43, 13, 4, 3, -1, 'Venta a Hab. 304', '2025-06-26 15:57:22'),
(44, 6, 15, 14, -1, 'Venta a Hab. 210', '2025-06-26 11:26:06'),
(45, 4, 3, 2, -1, 'Venta a Hab. 403', '2025-06-26 14:28:55'),
(46, 13, 3, 2, -1, 'Venta a Hab. 304', '2025-06-27 15:52:06'),
(47, 9, 9, 8, -1, 'Venta a Hab. 304', '2025-06-27 15:52:06'),
(48, 6, 14, 13, -1, 'Venta a Hab. 211', '2025-06-27 17:00:24'),
(49, 9, 8, 7, -1, 'Venta a Hab. 211', '2025-06-27 17:00:24'),
(50, 9, 7, 6, -1, 'Venta a Hab. 211', '2025-06-27 17:00:24'),
(51, 13, 2, 1, -1, 'Venta a Hab. 304', '2025-06-27 19:45:44'),
(52, 11, 9, 8, -1, 'Venta a Hab. 304', '2025-06-27 19:45:44'),
(53, 13, 1, 0, -1, 'Venta a Hab. 304', '2025-06-27 19:45:44'),
(54, 4, 2, 1, -1, 'Venta a Hab. 211', '2025-06-27 21:54:20'),
(55, 5, 13, 12, -1, 'Venta a Hab. 403', '2025-06-28 17:59:09'),
(56, 11, 8, 7, -1, 'Venta a Hab. 403', '2025-06-28 14:56:37'),
(57, 12, 22, 21, -1, 'Venta a Hab. 403', '2025-06-28 14:56:37'),
(58, 5, 12, 11, -1, 'Venta a Hab. 509', '2025-06-28 20:26:13'),
(59, 9, 6, 5, -1, 'Venta a Hab. 509', '2025-06-28 20:26:13'),
(60, 4, 1, 0, -1, 'Venta a Hab. 509', '2025-06-28 20:26:13'),
(61, 6, 13, 12, -1, 'Venta a Hab. 403', '2025-06-29 12:08:14'),
(62, 12, 21, 20, -1, 'Venta a Hab. 210', '2025-06-29 10:50:32'),
(63, 9, 5, 4, -1, 'Venta a Hab. 210', '2025-06-29 10:50:32'),
(64, 7, 6, 5, -1, 'Venta a Hab. 210', '2025-06-29 10:50:32'),
(65, 6, 12, 11, -1, 'Venta a Hab. 208', '2025-06-29 15:24:50'),
(66, 7, 5, 4, -1, 'Venta a Hab. 208', '2025-06-29 15:24:50'),
(67, 7, 4, 3, -1, 'Venta a Hab. 208', '2025-06-29 15:24:50'),
(68, 11, 7, 6, -1, 'Venta a Hab. 210', '2025-06-29 09:51:35'),
(69, 12, 20, 19, -1, 'Venta a Hab. 511', '2025-06-29 16:15:34'),
(70, 5, 11, 10, -1, 'Venta a Hab. 511', '2025-06-29 16:15:34'),
(71, 12, 19, 18, -1, 'Venta a Hab. 511', '2025-06-29 16:15:34'),
(72, 3, 0, 24, 24, 'Compra a proveedor', '2025-06-23 10:00:00'),
(73, 4, 0, 24, 24, 'Compra a proveedor', '2025-06-23 10:00:00'),
(74, 6, 11, 47, 36, 'Compra a proveedor', '2025-06-25 11:30:00'),
(75, 7, 3, 39, 36, 'Compra a proveedor', '2025-06-25 11:30:00'),
(76, 1, 0, 24, 24, 'Compra a proveedor', '2025-06-27 09:15:00'),
(77, 2, 0, 24, 24, 'Compra a proveedor', '2025-06-27 09:15:00'),
(78, 12, 18, 68, 50, 'Ajuste manual de inventario', '2025-06-27 09:15:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ocupaciones`
--

CREATE TABLE `ocupaciones` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `habitacion_id` varchar(10) NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora exactas del inicio de la ocupación.',
  `estadia_dias` int(11) NOT NULL,
  `monto_por_dia` decimal(10,2) NOT NULL,
  `monto_adicional_descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_total` decimal(10,2) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `taxi_info` varchar(100) DEFAULT NULL COMMENT 'Identificador del taxi (nombre, placa, etc.) que trajo al cliente.',
  `taxi_comision` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Monto de la comisión pagada al taxista por esta ocupación.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ocupaciones`
--

INSERT INTO `ocupaciones` (`id`, `cliente_id`, `habitacion_id`, `fecha_inicio`, `estadia_dias`, `monto_por_dia`, `monto_adicional_descuento`, `monto_total`, `activa`, `taxi_info`, `taxi_comision`) VALUES
(1, 1, '603', '2025-06-22 07:07:00', 2, 147.00, 0.00, 294.00, 0, 'OSCAR', 30.00),
(2, 2, '407', '2025-06-22 14:55:00', 2, 95.00, 0.00, 190.00, 0, 'AEREO', 10.00),
(3, 3, '209', '2025-06-22 21:10:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(4, 4, '312', '2025-06-23 09:27:00', 2, 120.00, 0.00, 240.00, 0, NULL, 0.00),
(5, 5, '403', '2025-06-24 00:30:00', 6, 110.00, 0.00, 660.00, 0, 'AEREO', 10.00),
(6, 6, '210', '2025-06-24 07:15:00', 6, 110.00, 0.00, 660.00, 0, 'AEREO', 10.00),
(7, 7, '504', '2025-06-24 19:22:00', 2, 75.00, 0.00, 150.00, 0, NULL, 0.00),
(8, 8, '410', '2025-06-24 20:00:00', 2, 116.00, 0.00, 232.00, 0, NULL, 0.00),
(9, 9, '404', '2025-06-24 20:00:00', 2, 79.00, 0.00, 158.00, 0, NULL, 0.00),
(10, 10, '213', '2025-06-24 20:45:00', 3, 130.00, 0.00, 390.00, 0, NULL, 0.00),
(11, 11, '103', '2025-06-24 20:45:00', 3, 130.00, 0.00, 390.00, 0, 'P-36', 10.00),
(12, 12, '507', '2025-06-25 09:00:00', 2, 116.00, 0.00, 232.00, 0, NULL, 0.00),
(13, 13, '304', '2025-06-25 13:02:00', 3, 77.33, 0.00, 231.99, 0, NULL, 0.00),
(14, 14, '501', '2025-06-25 13:36:00', 1, 168.00, 0.00, 168.00, 0, NULL, 0.00),
(15, 15, '409', '2025-06-25 18:15:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(16, 16, '405', '2025-06-25 19:00:00', 3, 167.33, 0.00, 501.99, 0, NULL, 0.00),
(17, 17, '411', '2025-06-25 19:00:00', 3, 105.33, 0.00, 315.99, 0, NULL, 0.00),
(18, 18, '204', '2025-06-25 20:08:00', 1, 75.00, 0.00, 75.00, 0, NULL, 0.00),
(19, 19, '310', '2025-06-25 21:45:00', 1, 100.00, 0.00, 100.00, 0, NULL, 0.00),
(20, 20, '402', '2025-06-25 23:00:00', 1, 120.00, 0.00, 120.00, 0, NULL, 0.00),
(21, 11, '103', '2025-06-24 20:45:00', 3, 130.00, 0.00, 390.00, 0, 'P-36', 10.00),
(22, 12, '507', '2025-06-25 09:00:00', 2, 116.00, 0.00, 232.00, 0, NULL, 0.00),
(23, 13, '304', '2025-06-25 13:02:00', 3, 77.33, 0.00, 231.99, 0, NULL, 0.00),
(24, 14, '501', '2025-06-25 13:36:00', 1, 168.00, 0.00, 168.00, 0, NULL, 0.00),
(25, 15, '409', '2025-06-25 18:15:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(26, 16, '405', '2025-06-25 19:00:00', 3, 167.33, 0.00, 501.99, 0, NULL, 0.00),
(27, 17, '411', '2025-06-25 19:00:00', 3, 105.33, 0.00, 315.99, 0, NULL, 0.00),
(28, 18, '204', '2025-06-25 20:08:00', 1, 75.00, 0.00, 75.00, 0, NULL, 0.00),
(29, 19, '310', '2025-06-25 21:45:00', 1, 100.00, 0.00, 100.00, 0, NULL, 0.00),
(30, 20, '402', '2025-06-25 23:00:00', 1, 120.00, 0.00, 120.00, 0, NULL, 0.00),
(33, 32, '313', '2025-06-26 03:55:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(34, 33, '410', '2025-06-26 09:18:00', 3, 110.00, 0.00, 330.00, 0, NULL, 0.00),
(35, 34, '302', '2025-06-26 14:31:00', 2, 142.50, 0.00, 285.00, 0, NULL, 0.00),
(36, 35, '309', '2025-06-26 17:02:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(37, 36, '409', '2025-06-26 18:32:00', 1, 95.00, 0.00, 95.00, 0, 'C/T', 10.00),
(38, 37, '507', '2025-06-26 19:04:00', 1, 95.00, 0.00, 95.00, 0, 'C/T', 15.00),
(39, 38, '404', '2025-06-26 19:08:00', 1, 150.00, 0.00, 150.00, 0, NULL, 0.00),
(40, 39, '104', '2025-06-26 19:08:00', 1, 118.00, 0.00, 118.00, 0, NULL, 0.00),
(41, 4, '403', '2025-06-26 21:10:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(42, 40, '211', '2025-06-27 08:57:00', 1, 120.00, 0.00, 120.00, 0, 'AEREO', 0.00),
(43, 41, '213', '2025-06-27 09:00:00', 1, 95.00, 0.00, 95.00, 0, 'AEREO', 0.00),
(44, 42, '204', '2025-06-27 16:35:00', 1, 95.00, 0.00, 95.00, 0, 'AEREO', 0.00),
(45, 43, '201', '2025-06-27 19:15:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(46, 11, '104', '2025-06-27 20:35:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(47, 10, '309', '2025-06-27 20:35:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(48, 46, '309', '2025-06-27 21:20:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(49, 47, '509', '2025-06-27 21:22:00', 2, 95.00, 0.00, 190.00, 0, NULL, 0.00),
(50, 48, '308', '2025-06-27 21:54:00', 1, 95.00, 0.00, 95.00, 0, NULL, 0.00),
(51, 49, '309', '2025-06-27 01:40:00', 1, 95.00, 0.00, 95.00, 0, 'AEREO', 0.00),
(52, 50, '308', '2025-06-28 01:40:00', 1, 162.00, 0.00, 162.00, 0, 'AEREO', 0.00),
(57, 55, '604', '2025-06-28 13:20:00', 1, 162.00, 0.00, 162.00, 0, NULL, 0.00),
(58, 56, '508', '2025-06-28 17:04:00', 1, 115.00, 0.00, 115.00, 0, NULL, 0.00),
(59, 57, '308', '2025-06-28 17:22:00', 1, 162.00, 0.00, 162.00, 0, NULL, 0.00),
(60, 58, '501', '2025-06-29 00:30:00', 1, 162.00, 0.00, 162.00, 0, NULL, 0.00),
(61, 58, '511', '2025-06-29 11:30:00', 1, 162.00, 0.00, 162.00, 0, NULL, 0.00),
(62, 59, '208', '2025-06-29 08:50:00', 2, 115.00, 0.00, 230.00, 0, NULL, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `ocupacion_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `comprobante` varchar(50) DEFAULT NULL,
  `numero_comprobante` varchar(50) DEFAULT NULL COMMENT 'El número o código de la boleta/factura.',
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `ocupacion_id`, `cliente_id`, `monto_pagado`, `metodo_pago`, `comprobante`, `numero_comprobante`, `fecha_pago`) VALUES
(1, 1, 1, 294.00, 'Efectivo', 'Factura', 'E001-4850', '2025-07-06 14:58:47'),
(2, 2, 2, 190.00, 'Efectivo', 'Factura', 'E001-4853', '2025-07-06 14:58:47'),
(3, 3, 3, 95.00, 'Efectivo', 'Factura', 'E001-4849', '2025-07-06 14:58:47'),
(4, 4, 4, 240.00, 'Efectivo', 'Factura', 'E001-4852', '2025-07-06 14:58:47'),
(5, 5, 5, 660.00, 'Efectivo', 'Factura', 'E001-4886', '2025-07-06 14:58:47'),
(6, 6, 6, 660.00, 'Efectivo', 'Boleta', 'E001-1301', '2025-07-06 14:58:47'),
(7, 7, 7, 150.00, 'Efectivo', 'Factura', 'E001-4856', '2025-07-06 14:58:47'),
(8, 8, 8, 232.00, 'Efectivo', 'Factura', 'E001-4858', '2025-07-06 14:58:47'),
(9, 9, 9, 158.00, 'Efectivo', 'Factura', 'E001-4858', '2025-07-06 14:58:47'),
(10, 10, 10, 390.00, 'Efectivo', 'Factura', 'E001-4871', '2025-07-06 14:58:47'),
(11, 11, 11, 390.00, 'Efectivo', 'Factura', 'E001-4870', '2025-07-06 15:32:17'),
(12, 12, 12, 232.00, 'Efectivo', 'Factura', 'E001-4874', '2025-07-06 15:32:17'),
(13, 13, 13, 231.99, 'Efectivo', 'Boleta', 'E001-1298', '2025-07-06 15:32:17'),
(14, 14, 14, 168.00, 'Efectivo', 'Factura', 'E001-4873', '2025-07-06 15:32:17'),
(15, 15, 15, 95.00, 'Efectivo', 'Factura', 'E001-4855', '2025-07-06 15:32:17'),
(16, 16, 16, 501.99, 'Efectivo', 'Factura', 'E001-4880', '2025-07-06 15:32:17'),
(17, 17, 17, 315.99, 'Efectivo', 'Factura', 'E001-4881', '2025-07-06 15:32:17'),
(18, 18, 18, 75.00, 'Efectivo', 'Factura', 'E001-4864', '2025-07-06 15:32:17'),
(19, 19, 19, 100.00, 'Efectivo', 'Factura', 'E001-4859', '2025-07-06 15:32:17'),
(20, 20, 20, 120.00, 'Efectivo', 'Boleta', 'E001-1291', '2025-07-06 15:32:17'),
(21, 21, 11, 390.00, 'Efectivo', 'Factura', 'E001-4870', '2025-07-06 15:32:17'),
(22, 22, 12, 232.00, 'Efectivo', 'Factura', 'E001-4874', '2025-07-06 15:32:17'),
(23, 23, 13, 231.99, 'Efectivo', 'Boleta', 'E001-1298', '2025-07-06 15:32:17'),
(24, 24, 14, 168.00, 'Efectivo', 'Factura', 'E001-4873', '2025-07-06 15:32:17'),
(25, 25, 15, 95.00, 'Efectivo', 'Factura', 'E001-4855', '2025-07-06 15:32:17'),
(26, 26, 16, 501.99, 'Efectivo', 'Factura', 'E001-4880', '2025-07-06 15:32:17'),
(27, 27, 17, 315.99, 'Efectivo', 'Factura', 'E001-4881', '2025-07-06 15:32:17'),
(28, 28, 18, 75.00, 'Efectivo', 'Factura', 'E001-4864', '2025-07-06 15:32:17'),
(29, 29, 19, 100.00, 'Efectivo', 'Factura', 'E001-4859', '2025-07-06 15:32:17'),
(30, 30, 20, 120.00, 'Efectivo', 'Boleta', 'E001-1291', '2025-07-06 15:32:17'),
(32, 33, 32, 95.00, 'Efectivo', 'Factura', 'E001-4863', '2025-07-08 11:12:57'),
(33, 34, 33, 330.00, 'Efectivo', 'Factura', 'E001-4887', '2025-07-08 11:12:57'),
(34, 35, 34, 285.00, 'Efectivo', 'Factura', 'E001-4879', '2025-07-08 11:12:57'),
(35, 36, 35, 95.00, 'Efectivo', 'Factura', 'E001-4864', '2025-07-08 11:12:57'),
(36, 37, 36, 95.00, 'Efectivo', 'Factura', 'E001-4862', '2025-07-08 11:12:57'),
(37, 38, 37, 95.00, 'Efectivo', 'Factura', 'E001-4865', '2025-07-08 11:12:57'),
(38, 39, 38, 150.00, 'Efectivo', 'Factura', 'E001-4868', '2025-07-08 11:12:57'),
(39, 40, 39, 118.00, 'Efectivo', 'Factura', 'E001-4869', '2025-07-08 11:12:57'),
(40, 41, 4, 95.00, 'Efectivo', 'Factura', 'E001-4866', '2025-07-08 11:12:57'),
(41, 42, 40, 120.00, 'Efectivo', 'Boleta', 'E001-1294', '2025-07-08 11:12:57'),
(42, 43, 41, 95.00, 'Efectivo', 'Factura', 'E001-4878', '2025-07-08 11:17:17'),
(43, 44, 42, 95.00, 'Efectivo', 'Factura', 'E001-4867', '2025-07-08 11:17:17'),
(44, 45, 43, 95.00, 'Efectivo', 'Factura', 'E001-4877', '2025-07-08 11:17:17'),
(45, 46, 11, 95.00, 'Efectivo', 'Factura', 'E001-4883', '2025-07-08 11:17:17'),
(46, 47, 10, 95.00, 'Efectivo', 'Factura', 'E001-4872', '2025-07-08 11:17:17'),
(47, 48, 46, 95.00, 'Efectivo', 'Boleta', 'E001-1295', '2025-07-08 11:17:17'),
(48, 49, 47, 190.00, 'Efectivo', 'Boleta', 'E001-1296', '2025-07-08 11:17:17'),
(49, 50, 48, 95.00, 'Efectivo', 'Factura', 'E001-4875', '2025-07-08 11:17:17'),
(50, 51, 49, 95.00, 'Efectivo', 'Factura', 'E001-4876', '2025-07-08 11:17:17'),
(51, 52, 50, 162.00, 'Efectivo', 'Factura', 'E001-1297', '2025-07-08 11:17:17'),
(55, 57, 55, 162.00, 'Efectivo', 'Factura', 'E001-4884', '2025-07-08 11:39:01'),
(56, 58, 56, 115.00, 'Efectivo', 'Factura', 'E001-4885', '2025-07-08 11:39:01'),
(57, 59, 57, 162.00, 'Efectivo', 'Boleta', 'E001-1300', '2025-07-08 11:39:01'),
(58, 60, 58, 162.00, 'Efectivo', 'Factura', 'E001-4891', '2025-07-08 11:39:01'),
(59, 61, 58, 162.00, 'Efectivo', 'Factura', 'E001-4891', '2025-07-08 11:39:01'),
(60, 62, 59, 230.00, 'Efectivo', 'Factura', 'E001-4890', '2025-07-08 11:39:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `stock`, `precio`) VALUES
(1, 'Corona', 24, 10.00),
(2, 'Cuzqueña', 24, 10.00),
(3, 'Coca Cola 500ml', 24, 5.00),
(4, 'Inka Kola 500ml', 24, 5.00),
(5, 'Fruvi 500ml', 10, 4.00),
(6, 'Agua sin gas 600ml', 47, 3.00),
(7, 'Agua con gas 600ml', 39, 3.00),
(8, 'Reacondicionador', 0, 2.50),
(9, 'Afeitador', 4, 3.00),
(10, 'Cepillo dental', 0, 4.00),
(11, 'Pasta Dental', 6, 4.00),
(12, 'Shampoo Sachet', 68, 2.50),
(13, 'Shampoo Grande', 0, 8.00),
(14, 'Queque', 0, 2.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_habitaciones`
--

CREATE TABLE `tipos_habitaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos_habitaciones`
--

INSERT INTO `tipos_habitaciones` (`id`, `nombre`) VALUES
(4, 'Cuádruple'),
(5, 'Doble'),
(2, 'Matrimonial'),
(1, 'Simple'),
(3, 'Triple');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `ocupacion_id` int(11) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pago_pendiente` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_venta` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla principal que agrupa una transacción de venta.';

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `ocupacion_id`, `monto_total`, `monto_pagado`, `pago_pendiente`, `fecha_venta`) VALUES
(1, 2, 15.00, 15.00, 0, '2025-06-22 13:48:06'),
(2, 3, 10.50, 10.50, 0, '2025-06-22 17:35:06'),
(3, 1, 10.50, 10.50, 0, '2025-06-22 17:19:56'),
(4, 1, 4.00, 4.00, 0, '2025-06-22 14:28:22'),
(5, 1, 5.00, 5.00, 0, '2025-06-22 21:38:21'),
(6, 2, 17.00, 17.00, 0, '2025-06-23 20:22:21'),
(7, 2, 10.50, 10.50, 0, '2025-06-23 10:02:03'),
(8, 1, 13.50, 13.50, 0, '2025-06-23 14:13:00'),
(9, 5, 10.00, 10.00, 0, '2025-06-24 18:19:35'),
(10, 6, 10.00, 10.00, 0, '2025-06-24 21:43:49'),
(11, 8, 16.50, 16.50, 0, '2025-06-24 14:13:21'),
(12, 19, 18.00, 18.00, 0, '2025-06-25 20:14:34'),
(13, 26, 9.00, 9.00, 0, '2025-06-25 18:01:08'),
(14, 26, 9.00, 9.00, 0, '2025-06-25 14:30:11'),
(15, 21, 14.50, 14.50, 0, '2025-06-25 13:42:18'),
(16, 10, 8.00, 8.00, 0, '2025-06-25 13:59:44'),
(17, 40, 7.50, 7.50, 0, '2025-06-26 15:17:06'),
(18, 13, 18.00, 18.00, 0, '2025-06-26 15:57:22'),
(19, 6, 3.00, 3.00, 0, '2025-06-26 11:26:06'),
(20, 41, 5.00, 5.00, 0, '2025-06-26 14:28:55'),
(21, 13, 11.00, 11.00, 0, '2025-06-27 15:52:06'),
(22, 42, 9.00, 9.00, 0, '2025-06-27 17:00:24'),
(23, 23, 20.00, 20.00, 0, '2025-06-27 19:45:44'),
(24, 42, 5.00, 5.00, 0, '2025-06-27 21:54:20'),
(25, 5, 4.00, 4.00, 0, '2025-06-28 17:59:09'),
(26, 5, 6.50, 6.50, 0, '2025-06-28 14:56:37'),
(27, 49, 12.00, 12.00, 0, '2025-06-28 20:26:13'),
(28, 5, 3.00, 3.00, 0, '2025-06-29 12:08:14'),
(29, 6, 8.50, 8.50, 0, '2025-06-29 10:50:32'),
(30, 62, 9.00, 9.00, 0, '2025-06-29 15:24:50'),
(31, 6, 4.00, 4.00, 0, '2025-06-29 09:51:35'),
(32, 61, 9.00, 9.00, 0, '2025-06-29 16:15:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_vendida` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL COMMENT 'Precio del producto al momento de la venta',
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de los productos vendidos en cada transacción.';

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad_vendida`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 1, 10.00, 10.00),
(2, 1, 3, 1, 5.00, 5.00),
(3, 2, 10, 1, 4.00, 4.00),
(4, 2, 11, 1, 4.00, 4.00),
(5, 2, 8, 1, 2.50, 2.50),
(6, 3, 12, 1, 2.50, 2.50),
(7, 3, 13, 1, 8.00, 8.00),
(8, 4, 10, 1, 4.00, 4.00),
(9, 5, 4, 1, 5.00, 5.00),
(10, 6, 5, 1, 4.00, 4.00),
(11, 6, 13, 1, 8.00, 8.00),
(12, 6, 3, 1, 5.00, 5.00),
(13, 7, 9, 1, 3.00, 3.00),
(14, 7, 4, 1, 5.00, 5.00),
(15, 7, 8, 1, 2.50, 2.50),
(16, 8, 13, 1, 8.00, 8.00),
(17, 8, 12, 1, 2.50, 2.50),
(18, 8, 9, 1, 3.00, 3.00),
(19, 9, 2, 1, 10.00, 10.00),
(20, 10, 3, 1, 5.00, 5.00),
(21, 10, 4, 1, 5.00, 5.00),
(22, 11, 8, 1, 2.50, 2.50),
(23, 11, 11, 1, 4.00, 4.00),
(24, 11, 1, 1, 10.00, 10.00),
(25, 12, 1, 1, 10.00, 10.00),
(26, 12, 3, 1, 5.00, 5.00),
(27, 12, 7, 1, 3.00, 3.00),
(28, 13, 7, 1, 3.00, 3.00),
(29, 13, 9, 1, 3.00, 3.00),
(30, 13, 7, 1, 3.00, 3.00),
(31, 14, 10, 1, 4.00, 4.00),
(32, 14, 3, 1, 5.00, 5.00),
(33, 15, 12, 1, 2.50, 2.50),
(34, 15, 13, 1, 8.00, 8.00),
(35, 15, 11, 1, 4.00, 4.00),
(36, 16, 4, 1, 5.00, 5.00),
(37, 16, 7, 1, 3.00, 3.00),
(38, 17, 8, 1, 2.50, 2.50),
(39, 17, 12, 1, 2.50, 2.50),
(40, 17, 8, 1, 2.50, 2.50),
(41, 18, 3, 1, 5.00, 5.00),
(42, 18, 3, 1, 5.00, 5.00),
(43, 18, 13, 1, 8.00, 8.00),
(44, 19, 6, 1, 3.00, 3.00),
(45, 20, 4, 1, 5.00, 5.00),
(46, 21, 13, 1, 8.00, 8.00),
(47, 21, 9, 1, 3.00, 3.00),
(48, 22, 6, 1, 3.00, 3.00),
(49, 22, 9, 1, 3.00, 3.00),
(50, 22, 9, 1, 3.00, 3.00),
(51, 23, 13, 1, 8.00, 8.00),
(52, 23, 11, 1, 4.00, 4.00),
(53, 23, 13, 1, 8.00, 8.00),
(54, 24, 4, 1, 5.00, 5.00),
(55, 25, 5, 1, 4.00, 4.00),
(56, 26, 11, 1, 4.00, 4.00),
(57, 26, 12, 1, 2.50, 2.50),
(58, 27, 5, 1, 4.00, 4.00),
(59, 27, 9, 1, 3.00, 3.00),
(60, 27, 4, 1, 5.00, 5.00),
(61, 28, 6, 1, 3.00, 3.00),
(62, 29, 12, 1, 2.50, 2.50),
(63, 29, 9, 1, 3.00, 3.00),
(64, 29, 7, 1, 3.00, 3.00),
(65, 30, 6, 1, 3.00, 3.00),
(66, 30, 7, 1, 3.00, 3.00),
(67, 30, 7, 1, 3.00, 3.00),
(68, 31, 11, 1, 4.00, 4.00),
(69, 32, 12, 1, 2.50, 2.50),
(70, 32, 5, 1, 4.00, 4.00),
(71, 32, 12, 1, 2.50, 2.50);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento_identidad` (`documento_identidad`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`numero_habitacion`),
  ADD KEY `tipo_id` (`tipo_id`);

--
-- Indices de la tabla `historial_stock`
--
ALTER TABLE `historial_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `ocupaciones`
--
ALTER TABLE `ocupaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `habitacion_id` (`habitacion_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ocupacion_id` (`ocupacion_id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `tipos_habitaciones`
--
ALTER TABLE `tipos_habitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ocupacion_id` (`ocupacion_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `historial_stock`
--
ALTER TABLE `historial_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `ocupaciones`
--
ALTER TABLE `ocupaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `tipos_habitaciones`
--
ALTER TABLE `tipos_habitaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD CONSTRAINT `habitaciones_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_habitaciones` (`id`);

--
-- Filtros para la tabla `historial_stock`
--
ALTER TABLE `historial_stock`
  ADD CONSTRAINT `historial_stock_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `ocupaciones`
--
ALTER TABLE `ocupaciones`
  ADD CONSTRAINT `ocupaciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ocupaciones_ibfk_2` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`numero_habitacion`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`ocupacion_id`) REFERENCES `ocupaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`ocupacion_id`) REFERENCES `ocupaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
