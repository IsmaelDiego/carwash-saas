-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-04-2026 a las 16:56:34
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `carwash-sys`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_sesiones`
--

CREATE TABLE `caja_sesiones` (
  `id_sesion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre_real` decimal(10,2) DEFAULT NULL,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `motivo_apertura` varchar(255) DEFAULT NULL,
  `id_rol_apertura` int(11) DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caja_sesiones`
--

INSERT INTO `caja_sesiones` (`id_sesion`, `id_usuario`, `monto_apertura`, `monto_cierre_real`, `monto_esperado`, `diferencia`, `estado`, `motivo_apertura`, `id_rol_apertura`, `fecha_apertura`, `fecha_cierre`) VALUES
(1, 4, 0.00, 0.00, 0.00, 0.00, 'CERRADA', NULL, NULL, '2026-03-26 16:18:58', '2026-03-27 09:35:58'),
(2, 4, 50.00, 100.00, 110.00, -10.00, 'CERRADA', NULL, NULL, '2026-03-27 11:26:03', '2026-03-27 12:19:14'),
(3, 4, 0.00, 771.00, 771.00, 0.00, 'CERRADA', NULL, NULL, '2026-03-27 12:59:11', '2026-03-31 11:24:33'),
(4, 4, 1220.00, 7223.50, 7223.50, 0.00, 'CERRADA', NULL, 2, '2026-03-31 11:44:13', '2026-04-10 17:22:32'),
(5, 4, 1000.00, 10000.00, 1134.00, 8866.00, 'CERRADA', NULL, 2, '2026-04-10 17:22:49', '2026-04-10 17:23:58'),
(6, 4, 1.00, 20.00, 11.00, 9.00, 'CERRADA', NULL, 2, '2026-04-10 17:41:10', '2026-04-10 17:41:58'),
(7, 4, 20.00, 58.50, 58.50, 0.00, 'CERRADA', NULL, 2, '2026-04-11 22:04:28', '2026-04-12 15:23:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_vehiculos`
--

CREATE TABLE `categorias_vehiculos` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `factor_precio` decimal(5,2) NOT NULL DEFAULT 1.00,
  `factor_tiempo` decimal(5,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_vehiculos`
--

INSERT INTO `categorias_vehiculos` (`id_categoria`, `nombre`, `factor_precio`, `factor_tiempo`) VALUES
(1, 'Auto Sedán', 1.00, 1.00),
(2, 'Camioneta SUV', 1.20, 1.25),
(3, 'Moto Lineal', 0.60, 0.50),
(4, 'Van / Minivan', 1.50, 1.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombres` varchar(150) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `sexo` char(1) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `telefono_alternativo` varchar(20) DEFAULT NULL,
  `estado_whatsapp` tinyint(1) DEFAULT 1,
  `puntos_acumulados` int(11) DEFAULT 0,
  `ya_canjeo_temporada_actual` tinyint(1) DEFAULT 0,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `dni`, `nombres`, `apellidos`, `sexo`, `telefono`, `telefono_alternativo`, `estado_whatsapp`, `puntos_acumulados`, `ya_canjeo_temporada_actual`, `fecha_registro`, `observaciones`) VALUES
(1, '00000000', 'Público General', '', NULL, NULL, NULL, 1, 0, 0, '2026-02-02 19:12:27', NULL),
(3, '40013213', 'ANA MARIA ', 'MENDOZA RUPAY', 'F', '973563350', NULL, 1, 5, 0, '2026-02-02 15:24:00', 'Sin observaciones'),
(5, '75692933', 'ISMAEL DIEGO', 'QUISPE MENDOZA', 'M', '973563350', NULL, 1, 3, 0, '2026-02-02 17:25:17', ''),
(7, '23394629', 'CONSTANTINO', 'MENDOZA FLORES', 'M', '973563350', NULL, 0, 0, 0, '2026-02-02 18:46:48', ''),
(8, '71875931', 'CAMILO ANTHONY', 'HUAYHUA CASTAÑEDA', 'M', '935 651 231', NULL, 1, 1, 0, '2026-02-03 17:22:15', ''),
(9, '17903382', 'CESAR', 'ACUÑA PERALTA', 'M', ' 973 596 626', NULL, 0, 1, 0, '2026-02-03 17:36:09', ''),
(10, '74589658', 'YENY', 'UCHARO OCHOA', 'M', '931 993 019', NULL, 0, 1, 0, '2026-02-03 17:36:56', ''),
(11, '72356894', 'IRMA KIARA', 'MORAN MICHILOT', 'M', '921 519 221', NULL, 0, 0, 0, '2026-02-03 17:38:04', ''),
(12, '73324115', 'MARCOS ROBERTO', 'PECHO LEANDRO', 'M', '906 829 934', NULL, 0, 0, 0, '2026-02-03 17:39:05', ''),
(13, '74236698', 'SUZETTI BELEN', 'QUISPE TAYPICAHUANA', 'M', '942 139 121', NULL, 0, 0, 0, '2026-02-03 17:41:05', ''),
(14, '74589634', 'LESLY HELEN', 'VILCHEZ SUERE', 'F', '973563350', NULL, 0, 0, 0, '2026-03-09 13:26:33', ''),
(15, '72256894', 'CAROLINA', 'LIZARRAGA HUAYANA', 'M', '973563350', NULL, 0, 0, 0, '2026-03-09 15:58:23', ''),
(16, '75326985', 'GAORI ISABEL', 'QUISPE CAHUANA', 'M', '973563350', NULL, 0, 0, 0, '2026-03-11 11:15:19', ''),
(17, '75896548', 'EDHYNSON EDUARDO', 'ESQUEN BARBOZA', 'M', '973563350', NULL, 0, 0, 0, '2026-03-11 11:16:27', ''),
(18, '47318373', 'SILVIO AMADOR', 'MENDOZA RUPAY', 'M', '973563350', NULL, 1, 1, 0, '2026-03-27 13:31:29', ''),
(19, '73495857', 'NATHALIE RAQUEL', 'PRADA GUERRERO', 'F', '985632147', NULL, 1, 0, 0, '2026-04-07 16:36:38', ''),
(20, '60282375', 'YEFERSON', 'MENDOZA ARISTE', 'F', '973563350', NULL, 1, 0, 0, '2026-04-10 15:16:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL,
  `nombre_negocio` varchar(100) NOT NULL DEFAULT 'Mi Carwash',
  `abreviatura` varchar(10) DEFAULT 'CW',
  `moneda` varchar(5) DEFAULT 'S/',
  `modo_sin_cajero` tinyint(1) DEFAULT 0,
  `meta_puntos_canje` int(11) DEFAULT 10,
  `logo` varchar(255) DEFAULT 'public/img/logo.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_configuracion`, `nombre_negocio`, `abreviatura`, `moneda`, `modo_sin_cajero`, `meta_puntos_canje`, `logo`) VALUES
(1, 'Carwash-sys', 'Carwash X', 'S/', 1, 10, 'public/uploads/logo.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_orden`
--

CREATE TABLE `detalle_orden` (
  `id_detalle` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `id_lote` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_orden`
--

INSERT INTO `detalle_orden` (`id_detalle`, `id_orden`, `id_servicio`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`, `id_lote`) VALUES
(1, 1, 1, NULL, 1, 300.00, 300.00, NULL),
(2, 2, 1, NULL, 1, 300.00, 300.00, NULL),
(3, 3, 2, NULL, 1, 15.00, 15.00, NULL),
(4, 4, 2, NULL, 1, 15.00, 15.00, NULL),
(5, 5, 2, NULL, 1, 15.00, 15.00, NULL),
(6, 6, NULL, 1, 1, 1.50, 1.50, NULL),
(7, 7, NULL, 1, 1, 1.50, 1.50, NULL),
(8, 8, 2, NULL, 1, 15.00, 15.00, NULL),
(9, 9, 2, NULL, 1, 15.00, 15.00, NULL),
(10, 10, 2, NULL, 1, 15.00, 15.00, NULL),
(11, 11, NULL, 1, 1, 1.50, 1.50, NULL),
(12, 12, 1, NULL, 1, 300.00, 300.00, NULL),
(13, 13, 2, NULL, 1, 15.00, 15.00, NULL),
(14, 13, NULL, 3, 1, 2.50, 2.50, NULL),
(15, 13, NULL, 1, 1, 3.00, 3.00, NULL),
(16, 14, 1, NULL, 1, 300.00, 300.00, NULL),
(17, 15, NULL, 3, 1, 2.50, 2.50, NULL),
(18, 16, NULL, 8, 5, 8.00, 40.00, 8),
(19, 16, NULL, 8, 2, 10.00, 20.00, 9),
(20, 17, NULL, 9, 1, 2.00, 2.00, 10),
(21, 18, NULL, 8, 2, 10.00, 20.00, 9),
(22, 19, 2, NULL, 1, 8.00, 8.00, NULL),
(23, 20, 2, NULL, 1, 10.00, 10.00, NULL),
(24, 21, 2, NULL, 1, 10.00, 10.00, NULL),
(25, 22, 1, NULL, 1, 200.00, 200.00, NULL),
(26, 23, 1, NULL, 1, 200.00, 200.00, NULL),
(27, 24, 2, NULL, 1, 10.00, 10.00, NULL),
(28, 25, 1, NULL, 1, 200.00, 200.00, NULL),
(29, 25, NULL, 2, 1, 20.00, 20.00, 2),
(30, 25, NULL, 1, 1, 3.00, 3.00, 1),
(31, 25, NULL, 5, 1, 15.00, 15.00, 5),
(32, 25, NULL, 6, 1, 1.00, 1.00, 6),
(33, 25, NULL, 8, 1, 10.00, 10.00, 9),
(34, 25, NULL, 8, 1, 10.00, 10.00, 9),
(35, 25, NULL, 5, 4, 15.00, 60.00, 5),
(36, 26, 1, NULL, 1, 200.00, 200.00, NULL),
(37, 27, 1, NULL, 1, 200.00, 200.00, NULL),
(38, 28, 2, NULL, 1, 10.00, 10.00, NULL),
(44, 34, 1, NULL, 1, 300.00, 300.00, NULL),
(45, 35, 2, NULL, 1, 15.00, 15.00, NULL),
(46, 36, 2, NULL, 1, 6.00, 6.00, NULL),
(47, 37, 2, NULL, 1, 15.00, 15.00, NULL),
(48, 38, 1, NULL, 1, 240.00, 240.00, NULL),
(49, 39, 2, NULL, 1, 15.00, 15.00, NULL),
(50, 40, 2, NULL, 1, 15.00, 15.00, NULL),
(51, 41, 2, NULL, 1, 12.00, 12.00, NULL),
(52, 42, 1, NULL, 1, 120.00, 120.00, NULL),
(53, 43, 1, NULL, 1, 300.00, 300.00, NULL),
(54, 44, 1, NULL, 1, 240.00, 240.00, NULL),
(55, 45, 2, NULL, 1, 15.00, 15.00, NULL),
(56, 46, 2, NULL, 1, 12.00, 12.00, NULL),
(57, 47, 1, NULL, 1, 240.00, 240.00, NULL),
(58, 48, 1, NULL, 1, 300.00, 300.00, NULL),
(59, 49, 2, NULL, 1, 15.00, 15.00, NULL),
(60, 50, 2, NULL, 1, 12.00, 12.00, NULL),
(61, 51, 2, NULL, 1, 12.00, 12.00, NULL),
(62, 52, 2, NULL, 1, 12.00, 12.00, NULL),
(63, 53, 2, NULL, 1, 15.00, 15.00, NULL),
(64, 54, 1, NULL, 1, 120.00, 120.00, NULL),
(65, 55, 2, NULL, 1, 6.00, 6.00, NULL),
(66, 55, NULL, 6, 5, 1.00, 5.00, 6),
(67, 55, NULL, 2, 5, 20.00, 100.00, 2),
(68, 55, NULL, 6, 5, 1.00, 5.00, 6),
(69, 55, NULL, 2, 5, 20.00, 100.00, 2),
(70, 55, NULL, 6, 5, 1.00, 5.00, 6),
(71, 55, NULL, 2, 5, 20.00, 100.00, 2),
(72, 41, NULL, 8, 4, 10.00, 40.00, 9),
(73, 56, 1, NULL, 1, 240.00, 240.00, NULL),
(74, 57, NULL, 9, 1, 2.00, 2.00, 10),
(75, 58, NULL, 6, 1, 1.00, 1.00, 6),
(76, 59, 2, NULL, 1, 12.00, 12.00, NULL),
(77, 60, 2, NULL, 1, 12.00, 12.00, NULL),
(78, 61, 2, NULL, 1, 15.00, 15.00, NULL),
(79, 62, 2, NULL, 1, 10.00, 10.00, NULL),
(80, 63, 1, NULL, 1, 200.00, 200.00, NULL),
(81, 64, 2, NULL, 1, 10.00, 10.00, NULL),
(82, 65, 2, NULL, 1, 10.00, 10.00, NULL),
(83, 66, 1, NULL, 1, 200.00, 200.00, NULL),
(84, 67, 2, NULL, 1, 10.00, 10.00, NULL),
(85, 68, 2, NULL, 1, 10.00, 10.00, NULL),
(86, 69, 2, NULL, 1, 10.00, 10.00, NULL),
(87, 70, 1, NULL, 1, 200.00, 200.00, NULL),
(88, 71, 2, NULL, 1, 10.00, 10.00, NULL),
(89, 72, 2, NULL, 1, 10.00, 10.00, NULL),
(90, 73, 2, NULL, 1, 10.00, 10.00, NULL),
(91, 74, 2, NULL, 1, 10.00, 10.00, NULL),
(96, 79, 2, NULL, 1, 10.00, 10.00, NULL),
(97, 80, 2, NULL, 1, 10.00, 10.00, NULL),
(98, 60, NULL, 1, 10, 3.00, 30.00, 1),
(99, 81, 2, NULL, 1, 10.00, 10.00, NULL),
(100, 82, 2, NULL, 1, 10.00, 10.00, NULL),
(101, 83, 2, NULL, 1, 10.00, 10.00, NULL),
(102, 84, 2, NULL, 1, 10.00, 10.00, NULL),
(103, 85, 2, NULL, 1, 10.00, 10.00, NULL),
(104, 86, 2, NULL, 1, 10.00, 10.00, NULL),
(105, 87, 2, NULL, 1, 10.00, 10.00, NULL),
(106, 88, 2, NULL, 1, 10.00, 10.00, NULL),
(107, 89, 2, NULL, 1, 10.00, 10.00, NULL),
(108, 90, 2, NULL, 1, 10.00, 10.00, NULL),
(109, 91, 2, NULL, 1, 10.00, 10.00, NULL),
(110, 92, 2, NULL, 1, 10.00, 10.00, NULL),
(111, 93, 2, NULL, 1, 10.00, 10.00, NULL),
(112, 94, 2, NULL, 1, 10.00, 10.00, NULL),
(113, 95, 2, NULL, 1, 10.00, 10.00, NULL),
(114, 96, 2, NULL, 1, 10.00, 10.00, NULL),
(115, 97, 2, NULL, 1, 10.00, 10.00, NULL),
(127, 109, 2, NULL, 1, 10.00, 10.00, NULL),
(128, 110, 2, NULL, 1, 10.00, 10.00, NULL),
(129, 111, 1, NULL, 1, 200.00, 200.00, NULL),
(134, 116, 1, NULL, 1, 200.00, 200.00, NULL),
(135, 117, 2, NULL, 1, 10.00, 10.00, NULL),
(136, 109, NULL, 9, 3, 2.00, 6.00, 10),
(137, 109, NULL, 9, 7, 2.00, 14.00, 11),
(141, 121, 1, NULL, 1, 200.00, 200.00, NULL),
(142, 122, 2, NULL, 1, 10.00, 10.00, NULL),
(143, 123, 2, NULL, 1, 10.00, 10.00, NULL),
(144, 123, NULL, 3, 1, 2.50, 2.50, 3),
(145, 96, NULL, 6, 1, 1.00, 1.00, 6),
(146, 93, NULL, 6, 1, 1.00, 1.00, 6),
(147, 91, NULL, 2, 1, 20.00, 20.00, 2),
(148, 91, NULL, 2, 1, 20.00, 20.00, 2),
(149, 124, 2, NULL, 1, 10.00, 10.00, NULL),
(150, 125, 2, NULL, 1, 10.00, 10.00, NULL),
(151, 91, NULL, 3, 1, 2.50, 2.50, 3),
(152, 126, 2, NULL, 1, 10.00, 10.00, NULL),
(153, 91, NULL, 6, 3, 1.00, 3.00, 6),
(154, 91, NULL, 9, 1, 2.00, 2.00, 11),
(155, 91, NULL, 3, 1, 2.50, 2.50, 3),
(156, 125, NULL, 2, 1, 20.00, 20.00, 2),
(157, 124, NULL, 1, 1, 3.00, 3.00, 1),
(158, 124, NULL, 6, 1, 1.00, 1.00, 6),
(159, 127, 2, NULL, 1, 10.00, 10.00, NULL),
(160, 128, 2, NULL, 1, 10.00, 10.00, NULL),
(161, 128, NULL, 9, 1, 2.00, 2.00, 11),
(162, 128, NULL, 1, 2, 3.00, 6.00, 1),
(163, 128, NULL, 6, 1, 1.00, 1.00, 6),
(164, 128, NULL, 3, 1, 2.50, 2.50, 3),
(165, 128, NULL, 7, 1, 2.00, 2.00, 7),
(166, 128, NULL, 5, 1, 15.00, 15.00, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tipo_gasto` enum('FIJO','VARIABLE') NOT NULL,
  `fecha_gasto` date NOT NULL,
  `id_insumo_origen` int(11) DEFAULT NULL,
  `id_usuario_registrador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id_gasto`, `descripcion`, `monto`, `tipo_gasto`, `fecha_gasto`, `id_insumo_origen`, `id_usuario_registrador`) VALUES
(1, 'Pago luz 1', 150.00, 'FIJO', '2026-03-09', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_uso_promociones`
--

CREATE TABLE `historial_uso_promociones` (
  `id_historial` int(11) NOT NULL,
  `id_promocion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_uso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_uso_promociones`
--

INSERT INTO `historial_uso_promociones` (`id_historial`, `id_promocion`, `id_cliente`, `fecha_uso`) VALUES
(1, 6, 3, '2026-03-09 13:17:23'),
(2, 7, 5, '2026-03-12 02:10:47'),
(3, 1, 3, '2026-03-17 01:14:19'),
(4, 8, 3, '2026-03-27 18:25:04'),
(5, 8, 9, '2026-03-31 16:00:12'),
(10, 8, 7, '2026-04-04 18:51:16'),
(22, 9, 8, '2026-04-07 16:57:06'),
(27, 9, 3, '2026-04-07 16:59:38'),
(31, 8, 8, '2026-04-10 15:07:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `id_insumo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `unidad_medida` varchar(20) DEFAULT 'Unidad',
  `costo_unitario` decimal(10,2) NOT NULL,
  `stock_actual` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`id_insumo`, `nombre`, `unidad_medida`, `costo_unitario`, `stock_actual`) VALUES
(1, 'Shampu cera', 'Unidad', 15.00, 10),
(2, 'Champu cera', 'Unidad', 15.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex_movimientos`
--

CREATE TABLE `kardex_movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_lote` int(11) DEFAULT NULL,
  `tipo` enum('ENTRADA','VENTA','MERMA','AJUSTE_SALIDA') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `id_orden` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `kardex_movimientos`
--

INSERT INTO `kardex_movimientos` (`id_movimiento`, `id_producto`, `id_lote`, `tipo`, `cantidad`, `referencia`, `id_orden`, `id_usuario`, `fecha`) VALUES
(1, 1, 1, 'ENTRADA', 26, 'Entrada Lote #1 (migración)', NULL, NULL, '2026-03-27 10:02:41'),
(2, 2, 2, 'ENTRADA', 20, 'Entrada Lote #2 (migración)', NULL, NULL, '2026-03-27 10:02:41'),
(3, 3, 3, 'ENTRADA', 98, 'Entrada Lote #3 (migración)', NULL, NULL, '2026-03-27 10:02:41'),
(5, 5, 5, 'ENTRADA', 180, 'Entrada Lote #5 (migración)', NULL, NULL, '2026-03-27 10:07:33'),
(6, 6, 6, 'ENTRADA', 25, 'Entrada Lote #6 (migración)', NULL, NULL, '2026-03-27 10:10:30'),
(7, 7, 7, 'ENTRADA', 5, 'Entrada Lote #7 (migración)', NULL, NULL, '2026-03-27 10:27:05'),
(8, 8, 8, 'ENTRADA', 5, 'Entrada Lote #8', NULL, NULL, '2026-03-27 11:18:10'),
(9, 8, 9, 'ENTRADA', 10, 'Entrada Lote #9', NULL, NULL, '2026-03-27 11:24:11'),
(10, 8, 8, 'VENTA', 5, 'Venta Orden #16', 16, NULL, '2026-03-27 11:26:23'),
(11, 8, 9, 'VENTA', 2, 'Venta Orden #16', 16, NULL, '2026-03-27 11:26:23'),
(12, 9, 10, 'ENTRADA', 5, 'Entrada Lote #10', NULL, NULL, '2026-03-27 11:43:13'),
(13, 9, 11, 'ENTRADA', 10, 'Entrada Lote #11', NULL, NULL, '2026-03-27 11:43:47'),
(14, 9, 10, 'VENTA', 1, 'Venta Orden #17', 17, 4, '2026-03-27 12:59:18'),
(15, 8, 9, 'VENTA', 2, 'Venta Orden #18', 18, 4, '2026-03-27 13:30:11'),
(16, 2, 2, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:21:21'),
(17, 1, 1, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:40:43'),
(18, 5, 5, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:47:29'),
(19, 6, 6, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:50:02'),
(20, 8, 9, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:50:02'),
(21, 8, 9, 'VENTA', 1, 'Venta Orden #25', 25, 4, '2026-03-27 17:50:35'),
(22, 5, 5, 'VENTA', 4, 'Venta Orden #25', 25, 4, '2026-03-27 18:03:14'),
(23, 6, 6, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:35:44'),
(24, 2, 2, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:35:44'),
(25, 6, 6, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:35:57'),
(26, 2, 2, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:35:57'),
(27, 6, 6, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:38:31'),
(28, 2, 2, 'VENTA', 5, 'Venta Orden #55', 55, 4, '2026-03-31 15:38:31'),
(29, 8, 9, 'VENTA', 4, 'Venta Orden #41', 41, 4, '2026-03-31 15:58:01'),
(30, 9, 10, 'VENTA', 1, 'Venta Orden #57', 57, 4, '2026-03-31 16:40:18'),
(31, 6, 6, 'VENTA', 1, 'Venta Orden #58', 58, 4, '2026-03-31 16:40:34'),
(32, 1, 1, 'VENTA', 10, 'Venta Orden #60', 60, 4, '2026-04-04 13:41:28'),
(33, 9, 10, 'VENTA', 3, 'Venta Orden #109', 109, 4, '2026-04-07 17:02:58'),
(34, 9, 11, 'VENTA', 7, 'Venta Orden #109', 109, 4, '2026-04-07 17:02:58'),
(35, 3, 3, 'VENTA', 1, 'Venta Orden #123', 123, 4, '2026-04-10 15:17:03'),
(36, 6, 6, 'VENTA', 1, 'Venta Orden #96', 96, 4, '2026-04-10 15:21:42'),
(37, 6, 6, 'VENTA', 1, 'Venta Orden #93', 93, 4, '2026-04-10 16:37:32'),
(38, 2, 2, 'VENTA', 1, 'Venta Orden #91', 91, 4, '2026-04-10 16:40:47'),
(39, 2, 2, 'VENTA', 1, 'Venta Orden #91', 91, 4, '2026-04-10 16:40:56'),
(40, 3, 3, 'VENTA', 1, 'Venta Orden #91', 91, 4, '2026-04-10 16:42:56'),
(41, 6, 6, 'VENTA', 3, 'Venta Orden #91', 91, 4, '2026-04-10 17:02:44'),
(42, 9, 11, 'VENTA', 1, 'Venta Orden #91', 91, 4, '2026-04-10 17:02:44'),
(43, 3, 3, 'VENTA', 1, 'Venta Orden #91', 91, 4, '2026-04-10 17:02:44'),
(44, 2, 2, 'VENTA', 1, 'Venta Orden #125', 125, 4, '2026-04-10 17:23:25'),
(45, 1, 1, 'VENTA', 1, 'Venta Orden #124', 124, 4, '2026-04-10 17:23:36'),
(46, 6, 6, 'VENTA', 1, 'Venta Orden #124', 124, 4, '2026-04-10 17:23:36'),
(47, 9, 11, 'VENTA', 1, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(48, 1, 1, 'VENTA', 2, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(49, 6, 6, 'VENTA', 1, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(50, 3, 3, 'VENTA', 1, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(51, 7, 7, 'VENTA', 1, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(52, 5, 5, 'VENTA', 1, 'Venta Orden #128', 128, 4, '2026-04-11 22:05:00'),
(53, 8, 12, 'ENTRADA', 26, 'Entrada Lote #12', NULL, NULL, '2026-04-14 09:51:56'),
(54, 9, 13, 'ENTRADA', 80, 'Entrada Lote #13', NULL, NULL, '2026-04-14 09:52:23'),
(55, 6, 14, 'ENTRADA', 12, 'Entrada Lote #14', NULL, NULL, '2026-04-14 09:52:33'),
(56, 2, 15, 'ENTRADA', 100, 'Entrada Lote #15', NULL, NULL, '2026-04-14 09:52:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_recuperacion`
--

CREATE TABLE `notificaciones_recuperacion` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `estado` enum('PENDIENTE','ATENDIDO') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones_recuperacion`
--

INSERT INTO `notificaciones_recuperacion` (`id_notificacion`, `id_usuario`, `fecha_pedido`, `estado`) VALUES
(1, 6, '2026-03-17 07:31:40', 'ATENDIDO'),
(2, 1, '2026-03-17 08:04:23', 'ATENDIDO'),
(3, 2, '2026-03-17 08:12:56', 'ATENDIDO'),
(4, 6, '2026-03-17 08:13:35', 'ATENDIDO'),
(5, 6, '2026-03-17 08:36:59', 'ATENDIDO'),
(6, 4, '2026-04-10 17:27:38', 'ATENDIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id_orden` int(11) NOT NULL,
  `id_temporada` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_promocion` int(11) DEFAULT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `id_usuario_cajero` int(11) DEFAULT NULL,
  `estado` enum('EN_COLA','EN_ESPERA','EN_PROCESO','POR_COBRAR','FINALIZADO','ANULADO') DEFAULT 'EN_COLA',
  `ubicacion_en_local` varchar(50) DEFAULT NULL,
  `total_servicios` decimal(10,2) DEFAULT 0.00,
  `total_productos` decimal(10,2) DEFAULT 0.00,
  `descuento_promo` decimal(10,2) DEFAULT 0.00,
  `descuento_puntos` decimal(10,2) DEFAULT 0.00,
  `total_final` decimal(10,2) DEFAULT 0.00,
  `tiempo_total_estimado` int(11) DEFAULT 30,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `id_token_autorizacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `id_caja_sesion` int(11) DEFAULT NULL,
  `fecha_inicio_proceso` datetime DEFAULT NULL,
  `fecha_fin_proceso` datetime DEFAULT NULL,
  `estado_pago` enum('PENDIENTE','PAGADO','PARCIAL') DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_temporada`, `id_cliente`, `id_vehiculo`, `id_promocion`, `id_usuario_creador`, `id_usuario_cajero`, `estado`, `ubicacion_en_local`, `total_servicios`, `total_productos`, `descuento_promo`, `descuento_puntos`, `total_final`, `tiempo_total_estimado`, `motivo_anulacion`, `id_token_autorizacion`, `fecha_creacion`, `fecha_cierre`, `id_caja_sesion`, `fecha_inicio_proceso`, `fecha_fin_proceso`, `estado_pago`) VALUES
(1, 6, 9, 2, NULL, 3, 4, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, 30, NULL, NULL, '2026-03-09 12:06:33', '2026-03-09 12:08:24', NULL, NULL, NULL, 'PENDIENTE'),
(2, 6, 5, 2, NULL, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, 30, NULL, 1, '2026-03-09 12:11:01', '2026-03-09 12:11:47', NULL, NULL, NULL, 'PENDIENTE'),
(3, 6, 3, 2, NULL, 3, 3, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, 30, NULL, NULL, '2026-03-09 12:32:33', '2026-03-09 12:33:18', NULL, NULL, NULL, 'PENDIENTE'),
(4, 6, 10, 2, NULL, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, 30, NULL, NULL, '2026-03-09 12:38:29', '2026-03-09 12:40:12', NULL, NULL, NULL, 'PENDIENTE'),
(5, 6, 8, 2, NULL, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, 30, NULL, NULL, '2026-03-09 12:43:43', '2026-03-09 12:52:37', NULL, NULL, NULL, 'PENDIENTE'),
(6, 6, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, 30, NULL, NULL, '2026-03-09 12:52:31', '2026-03-09 12:52:31', NULL, NULL, NULL, 'PENDIENTE'),
(7, 6, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, 30, NULL, NULL, '2026-03-09 12:52:55', '2026-03-09 12:52:55', NULL, NULL, NULL, 'PENDIENTE'),
(8, 6, 3, 2, NULL, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, 30, NULL, NULL, '2026-03-09 12:56:41', '2026-03-09 13:02:31', NULL, NULL, NULL, 'PENDIENTE'),
(9, 6, 3, 3, NULL, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 1.50, 0.00, 13.50, 30, NULL, NULL, '2026-03-09 13:17:23', '2026-03-09 13:18:01', NULL, NULL, NULL, 'PENDIENTE'),
(10, 6, 3, 3, NULL, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 0.00, 0.00, 15.00, 30, NULL, NULL, '2026-03-09 13:20:45', '2026-03-09 13:20:59', NULL, NULL, NULL, 'PENDIENTE'),
(11, 6, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, 30, NULL, NULL, '2026-03-09 13:39:24', '2026-03-09 13:39:24', NULL, NULL, NULL, 'PENDIENTE'),
(12, 7, 5, 2, NULL, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 150.00, 0.00, 150.00, 30, NULL, 3, '2026-03-12 02:10:47', '2026-03-12 02:11:25', NULL, NULL, NULL, 'PENDIENTE'),
(13, 7, 5, 2, NULL, 3, 3, 'FINALIZADO', '', 15.00, 5.50, 0.00, 0.00, 23.50, 30, NULL, 4, '2026-03-16 15:00:55', '2026-03-16 15:02:13', NULL, NULL, NULL, 'PENDIENTE'),
(14, 7, 3, 3, NULL, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 30.00, 0.00, 270.00, 30, NULL, NULL, '2026-03-17 01:14:19', '2026-03-17 01:14:53', NULL, NULL, NULL, 'PENDIENTE'),
(15, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 2.50, 0.00, 0.00, 2.50, 30, NULL, NULL, '2026-03-26 15:38:19', '2026-03-26 15:38:19', NULL, NULL, NULL, 'PENDIENTE'),
(16, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 60.00, 0.00, 0.00, 60.00, 30, NULL, NULL, '2026-03-27 11:26:23', '2026-03-27 11:26:23', 2, NULL, NULL, 'PENDIENTE'),
(17, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 2.00, 0.00, 0.00, 2.00, 30, NULL, NULL, '2026-03-27 12:59:18', '2026-03-27 12:59:18', 3, NULL, NULL, 'PENDIENTE'),
(18, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 20.00, 0.00, 0.00, 20.00, 30, NULL, NULL, '2026-03-27 13:30:11', '2026-03-27 13:30:11', 3, NULL, NULL, 'PENDIENTE'),
(19, 8, 18, 7, NULL, 3, 3, 'FINALIZADO', '', 8.00, 0.00, 0.00, 0.00, 8.00, 30, NULL, NULL, '2026-03-27 13:33:56', '2026-03-27 13:34:26', NULL, NULL, NULL, 'PENDIENTE'),
(20, 8, 18, 7, NULL, 4, 4, 'FINALIZADO', '', 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-03-27 14:29:42', '2026-03-27 14:40:23', 3, NULL, NULL, 'PENDIENTE'),
(21, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', '', 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-03-27 14:40:41', '2026-03-27 14:40:56', 3, NULL, NULL, 'PENDIENTE'),
(22, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', '', 200.00, 0.00, 0.00, 0.00, 200.00, 30, NULL, NULL, '2026-03-27 14:45:23', '2026-03-31 11:24:10', 3, NULL, NULL, 'PENDIENTE'),
(23, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', '', 200.00, 0.00, 0.00, 0.00, 200.00, 30, NULL, NULL, '2026-03-27 14:45:55', '2026-03-27 14:47:24', 3, NULL, NULL, 'PENDIENTE'),
(24, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', '', 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-03-27 14:49:50', '2026-03-31 11:24:07', 3, NULL, NULL, 'PENDIENTE'),
(25, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', NULL, 200.00, 119.00, 0.00, 0.00, 319.00, 30, NULL, NULL, '2026-03-27 15:41:30', '2026-03-31 11:24:08', 3, NULL, NULL, 'PENDIENTE'),
(26, 8, 3, 3, 8, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 200.00, 0.00, 0.00, 30, NULL, NULL, '2026-03-27 18:25:04', '2026-03-31 11:24:13', 3, NULL, NULL, 'PENDIENTE'),
(27, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 0.00, 0.00, 200.00, 30, NULL, NULL, '2026-03-31 12:05:57', '2026-03-31 12:12:09', 4, '2026-03-31 19:05:57', NULL, 'PENDIENTE'),
(28, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-03-31 12:15:31', '2026-03-31 12:30:59', 4, '2026-03-31 19:15:31', NULL, 'PENDIENTE'),
(34, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 300.00, 0.00, 0.00, 0.00, 300.00, 45, NULL, NULL, '2026-03-31 12:29:41', '2026-03-31 12:45:08', 4, '2026-03-31 19:29:41', NULL, 'PENDIENTE'),
(35, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 12:31:38', '2026-03-31 12:40:32', 4, '2026-03-31 19:31:38', NULL, 'PENDIENTE'),
(36, 8, 12, 10, NULL, 4, 4, 'FINALIZADO', NULL, 6.00, 0.00, 0.00, 0.00, 6.00, 15, NULL, NULL, '2026-03-31 12:32:05', '2026-03-31 12:44:55', 4, '2026-03-31 12:35:12', NULL, 'PENDIENTE'),
(37, 8, 7, 11, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 12:35:06', '2026-03-31 12:47:42', 4, '2026-03-31 12:38:09', NULL, 'PENDIENTE'),
(38, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 240.00, 0.00, 0.00, 0.00, 240.00, 38, NULL, NULL, '2026-03-31 12:46:04', '2026-03-31 12:50:38', 4, '2026-03-31 19:46:04', NULL, 'PENDIENTE'),
(39, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 12:46:43', '2026-03-31 12:47:50', 4, '2026-03-31 19:46:43', NULL, 'PENDIENTE'),
(40, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 12:50:45', '2026-03-31 16:01:46', 4, '2026-03-31 19:50:45', NULL, 'PENDIENTE'),
(41, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 12.00, 40.00, 0.00, 0.00, 52.00, 38, NULL, NULL, '2026-03-31 13:02:25', '2026-03-31 16:01:55', 4, '2026-03-31 20:02:25', NULL, 'PENDIENTE'),
(42, 8, 12, 10, NULL, 4, 4, 'FINALIZADO', NULL, 120.00, 0.00, 0.00, 0.00, 120.00, 15, NULL, NULL, '2026-03-31 13:04:58', '2026-03-31 18:02:51', 4, '2026-03-31 16:00:20', NULL, 'PENDIENTE'),
(43, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', NULL, 300.00, 0.00, 0.00, 0.00, 300.00, 45, NULL, NULL, '2026-03-31 13:06:31', '2026-03-31 18:02:47', 4, '2026-03-31 16:01:52', NULL, 'PENDIENTE'),
(44, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 240.00, 0.00, 0.00, 0.00, 240.00, 38, NULL, NULL, '2026-03-31 13:17:41', '2026-03-31 18:02:51', 4, '2026-03-31 16:34:06', NULL, 'PENDIENTE'),
(45, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 13:18:05', '2026-03-31 18:02:52', 4, '2026-03-31 16:34:07', NULL, 'PENDIENTE'),
(46, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 12.00, 0.00, 0.00, 0.00, 12.00, 38, NULL, NULL, '2026-03-31 13:25:27', '2026-03-31 18:07:41', 4, '2026-03-31 18:02:39', NULL, 'PENDIENTE'),
(47, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 240.00, 0.00, 0.00, 0.00, 240.00, 38, NULL, NULL, '2026-03-31 13:28:54', '2026-03-31 18:07:41', 4, '2026-03-31 18:02:42', NULL, 'PENDIENTE'),
(48, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 300.00, 0.00, 0.00, 0.00, 300.00, 45, NULL, NULL, '2026-03-31 13:35:18', '2026-03-31 18:07:40', 4, '2026-03-31 18:07:25', NULL, 'PENDIENTE'),
(49, 8, 7, 11, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 13:36:54', '2026-03-31 18:07:39', 4, '2026-03-31 18:07:26', NULL, 'PENDIENTE'),
(50, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 12.00, 0.00, 0.00, 0.00, 12.00, 38, NULL, NULL, '2026-03-31 13:39:20', '2026-03-31 18:07:38', 4, '2026-03-31 18:07:27', NULL, 'PENDIENTE'),
(51, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 12.00, 0.00, 0.00, 0.00, 12.00, 38, NULL, NULL, '2026-03-31 13:39:38', '2026-03-31 18:07:38', 4, '2026-03-31 18:07:28', NULL, 'PENDIENTE'),
(52, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 12.00, 0.00, 0.00, 0.00, 12.00, 38, NULL, NULL, '2026-03-31 13:40:26', '2026-03-31 18:07:35', 4, '2026-03-31 18:07:29', NULL, 'PENDIENTE'),
(53, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 13:41:22', '2026-03-31 18:07:35', 4, '2026-03-31 18:07:30', NULL, 'PENDIENTE'),
(54, 8, 18, 7, NULL, 4, 4, 'FINALIZADO', NULL, 120.00, 0.00, 0.00, 0.00, 120.00, 15, NULL, NULL, '2026-03-31 15:29:17', '2026-03-31 15:30:06', 4, '2026-03-31 15:29:29', NULL, 'PENDIENTE'),
(55, 8, 18, 7, NULL, 4, 4, 'FINALIZADO', NULL, 6.00, 105.00, 0.00, 0.00, 111.00, 15, NULL, NULL, '2026-03-31 15:30:55', '2026-03-31 16:03:24', 4, '2026-03-31 15:38:56', NULL, 'PENDIENTE'),
(56, 8, 9, 12, 8, 4, 4, 'FINALIZADO', NULL, 240.00, 0.00, 240.00, 0.00, 0.00, 38, NULL, NULL, '2026-03-31 16:00:12', '2026-03-31 16:01:04', 4, '2026-03-31 16:00:27', NULL, 'PENDIENTE'),
(57, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 2.00, 0.00, 0.00, 2.00, 30, NULL, NULL, '2026-03-31 16:40:18', '2026-03-31 16:40:18', 4, NULL, NULL, 'PENDIENTE'),
(58, 8, 1, NULL, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.00, 0.00, 0.00, 1.00, 30, NULL, NULL, '2026-03-31 16:40:34', '2026-03-31 16:40:34', 4, NULL, NULL, 'PENDIENTE'),
(59, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', '', 12.00, 0.00, 0.00, 0.00, 12.00, 38, NULL, NULL, '2026-03-31 19:20:07', '2026-04-04 14:05:27', 4, '2026-04-01 02:20:07', NULL, 'PENDIENTE'),
(60, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', '', 12.00, 30.00, 0.00, 0.00, 42.00, 38, NULL, NULL, '2026-03-31 19:21:33', '2026-04-04 14:05:07', 4, '2026-04-01 02:21:33', NULL, 'PENDIENTE'),
(61, 8, 5, 2, NULL, 4, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, 45, NULL, NULL, '2026-03-31 19:24:29', '2026-04-04 19:19:02', 4, '2026-04-04 19:03:49', '2026-04-04 19:16:56', 'PENDIENTE'),
(62, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-03-31 19:26:00', '2026-04-04 12:14:14', 4, NULL, NULL, 'PENDIENTE'),
(63, 8, 12, 10, NULL, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 0.00, 0.00, 200.00, 30, NULL, NULL, '2026-03-31 19:33:27', '2026-04-04 14:10:04', 4, NULL, NULL, 'PENDIENTE'),
(64, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 12:55:49', '2026-04-04 19:19:13', 4, '2026-04-04 12:55:49', '2026-04-04 19:03:49', 'PAGADO'),
(65, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 12:56:32', '2026-04-10 17:22:55', 5, '2026-04-04 19:16:56', '2026-04-07 17:01:37', 'PAGADO'),
(66, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 0.00, 0.00, 200.00, 37, NULL, NULL, '2026-04-04 13:00:02', '2026-04-10 14:22:59', 4, '2026-04-04 13:00:02', '2026-04-10 14:17:10', 'PAGADO'),
(67, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:01:09', '2026-04-04 19:17:00', 4, '2026-04-04 13:01:09', '2026-04-04 19:03:47', 'PAGADO'),
(68, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:04:30', '2026-04-10 17:03:34', 4, '2026-04-07 17:01:37', '2026-04-10 14:17:14', 'PAGADO'),
(69, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:08:46', '2026-04-10 17:03:33', 4, '2026-04-10 13:18:56', '2026-04-10 14:17:02', 'PAGADO'),
(70, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 0.00, 0.00, 200.00, 37, NULL, NULL, '2026-04-04 13:08:58', '2026-04-10 17:03:33', 4, '2026-04-10 14:17:02', '2026-04-10 14:17:03', 'PAGADO'),
(71, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 30, NULL, NULL, '2026-04-04 13:14:41', '2026-04-04 19:21:10', 4, NULL, '2026-04-04 19:03:41', 'PENDIENTE'),
(72, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 13:24:32', '2026-04-10 17:03:33', 4, '2026-04-10 14:17:03', '2026-04-10 14:17:07', 'PAGADO'),
(73, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 13:27:00', '2026-04-10 17:03:33', 4, '2026-04-10 14:17:07', '2026-04-10 14:20:31', 'PAGADO'),
(74, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:29:51', '2026-04-10 17:03:33', 4, '2026-04-10 14:17:10', '2026-04-10 14:20:34', 'PAGADO'),
(79, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:31:14', '2026-04-10 17:03:33', 4, '2026-04-10 14:17:14', '2026-04-10 14:22:21', 'PAGADO'),
(80, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 13:37:36', '2026-04-10 17:03:32', 4, '2026-04-10 14:20:31', '2026-04-10 14:25:35', 'PAGADO'),
(81, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:05:19', '2026-04-10 17:03:32', 4, '2026-04-10 14:20:34', '2026-04-10 14:25:33', 'PAGADO'),
(82, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:12:05', '2026-04-10 17:03:32', 4, '2026-04-10 14:22:21', '2026-04-10 14:25:36', 'PAGADO'),
(83, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:17:42', '2026-04-10 17:03:32', 4, '2026-04-10 14:25:33', '2026-04-10 14:25:37', 'PAGADO'),
(84, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:19:31', '2026-04-10 17:03:32', 4, '2026-04-10 14:25:35', '2026-04-10 14:25:38', 'PAGADO'),
(85, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:21:40', '2026-04-10 17:03:32', 4, '2026-04-10 14:25:36', '2026-04-10 14:25:38', 'PAGADO'),
(86, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 14:22:45', '2026-04-10 17:03:31', 4, '2026-04-10 14:25:37', '2026-04-10 14:25:39', 'PAGADO'),
(87, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:24:21', '2026-04-10 17:03:31', 4, '2026-04-10 14:25:38', '2026-04-10 14:25:41', 'PAGADO'),
(88, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:26:04', '2026-04-10 17:03:31', 4, '2026-04-10 14:25:38', '2026-04-10 14:25:41', 'PAGADO'),
(89, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 14:28:14', '2026-04-10 17:03:31', 4, '2026-04-10 14:25:39', '2026-04-10 14:25:42', 'PAGADO'),
(90, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:34:54', '2026-04-10 17:03:29', 4, '2026-04-10 14:25:41', '2026-04-10 14:25:43', 'PAGADO'),
(91, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 50.00, 0.00, 0.00, 60.00, 45, NULL, NULL, '2026-04-04 14:37:49', '2026-04-10 17:22:51', 5, '2026-04-10 14:25:41', '2026-04-10 14:25:43', 'PAGADO'),
(92, 8, 3, 3, NULL, 4, NULL, 'ANULADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, 'Cliente insatiffecho', 8, '2026-04-04 14:39:21', NULL, 4, '2026-04-10 14:25:42', '2026-04-10 14:25:45', 'PAGADO'),
(93, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 1.00, 0.00, 0.00, 11.00, 45, NULL, NULL, '2026-04-04 14:39:59', '2026-04-10 17:03:20', 4, '2026-04-10 14:25:43', '2026-04-10 14:25:46', 'PAGADO'),
(94, 8, 3, 3, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-04 14:42:57', '2026-04-10 15:06:04', 4, '2026-04-10 14:25:43', '2026-04-10 14:25:47', 'PAGADO'),
(95, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 14:44:51', '2026-04-10 15:06:01', 4, '2026-04-10 14:25:45', '2026-04-10 14:50:19', 'PAGADO'),
(96, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 1.00, 0.00, 0.00, 11.00, 45, NULL, NULL, '2026-04-04 18:36:58', '2026-04-10 15:23:58', 4, '2026-04-10 14:25:46', '2026-04-10 14:50:12', 'PAGADO'),
(97, 8, 7, 11, 8, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 45, NULL, NULL, '2026-04-04 18:51:16', '2026-04-10 14:50:59', 4, '2026-04-10 14:25:47', '2026-04-10 14:25:53', 'PENDIENTE'),
(109, 8, 9, 12, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 20.00, 0.00, 0.00, 30.00, 37, NULL, NULL, '2026-04-04 18:52:05', '2026-04-10 13:19:02', 4, '2026-04-04 18:58:22', '2026-04-10 13:18:56', 'PENDIENTE'),
(110, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-04 18:56:19', '2026-04-10 14:52:56', 4, '2026-04-10 14:25:53', '2026-04-10 14:50:16', 'PENDIENTE'),
(111, 8, 8, 8, 9, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 50.00, 0.00, 150.00, 37, NULL, NULL, '2026-04-07 16:57:06', '2026-04-10 14:53:08', 4, '2026-04-10 14:50:12', '2026-04-10 14:50:19', 'PAGADO'),
(116, 8, 3, 3, 9, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 50.00, 0.00, 150.00, 45, NULL, NULL, '2026-04-07 16:59:38', '2026-04-10 14:52:47', 4, '2026-04-10 14:50:16', '2026-04-10 14:50:21', 'PAGADO'),
(117, 8, 9, 12, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-07 17:00:37', '2026-04-10 17:22:58', 5, '2026-04-10 14:50:19', '2026-04-10 14:50:22', 'PAGADO'),
(121, 8, 8, 8, 8, 4, 4, 'FINALIZADO', NULL, 200.00, 0.00, 200.00, 0.00, 0.00, 37, NULL, NULL, '2026-04-10 15:07:46', '2026-04-10 17:23:09', 5, '2026-04-10 15:07:46', '2026-04-10 17:23:06', 'PAGADO'),
(122, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-10 15:09:25', '2026-04-10 17:23:46', 5, '2026-04-10 15:09:25', '2026-04-10 17:23:00', 'PAGADO'),
(123, 8, 20, 13, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 2.50, 0.00, 0.00, 12.50, 37, NULL, NULL, '2026-04-10 15:16:38', '2026-04-10 15:17:25', 4, '2026-04-10 15:16:38', '2026-04-10 15:17:16', 'PAGADO'),
(124, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 4.00, 0.00, 0.00, 14.00, 45, NULL, NULL, '2026-04-10 16:41:59', '2026-04-10 17:23:50', 5, '2026-04-10 16:41:59', '2026-04-10 17:23:40', 'PAGADO'),
(125, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 20.00, 0.00, 0.00, 30.00, 45, NULL, NULL, '2026-04-10 16:42:14', '2026-04-10 17:23:42', 5, '2026-04-10 17:23:00', '2026-04-10 17:23:02', 'PAGADO'),
(126, 8, 3, 9, 8, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 45, NULL, NULL, '2026-04-10 16:54:06', '2026-04-10 17:23:12', 5, '2026-04-10 17:23:02', '2026-04-10 17:23:04', 'PAGADO'),
(127, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-10 17:41:18', '2026-04-10 17:41:51', 6, '2026-04-10 17:41:18', '2026-04-10 17:41:49', 'PAGADO'),
(128, 8, 8, 8, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 28.50, 0.00, 0.00, 38.50, 37, NULL, NULL, '2026-04-11 22:04:44', '2026-04-11 22:05:09', 7, '2026-04-11 22:04:44', '2026-04-11 22:05:03', 'PAGADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_empleados`
--

CREATE TABLE `pagos_empleados` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('SALARIO','ADELANTO','BONO','DESCUENTO') NOT NULL DEFAULT 'SALARIO',
  `monto` decimal(10,2) NOT NULL,
  `periodo` varchar(20) DEFAULT NULL COMMENT 'Ej: 2026-03',
  `estado` enum('PENDIENTE','PAGADO','RETRASADO') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_programada` date NOT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `id_admin_registrador` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_empleados`
--

INSERT INTO `pagos_empleados` (`id_pago`, `id_usuario`, `tipo`, `monto`, `periodo`, `estado`, `fecha_programada`, `fecha_pago`, `observaciones`, `id_admin_registrador`, `fecha_creacion`) VALUES
(7, 3, 'SALARIO', 2000.00, 'Abril', 'PAGADO', '2026-03-09', '2026-03-09 15:07:15', NULL, 2, '2026-03-09 15:13:36'),
(8, 4, 'ADELANTO', 2000.00, '2026-04', 'PAGADO', '2026-03-09', '2026-03-09 21:19:25', '', 2, '2026-03-09 15:19:25'),
(9, 5, 'BONO', 20.00, '2026-04', 'PAGADO', '2026-03-21', '2026-03-16 22:54:47', '', 1, '2026-03-16 16:51:30'),
(10, 3, 'ADELANTO', 20000.00, '2026-04', 'PAGADO', '2026-03-17', '2026-03-16 22:55:40', '', 1, '2026-03-16 16:55:40'),
(11, 9, 'ADELANTO', 2.00, '2026-08', 'PAGADO', '2026-03-25', '2026-03-16 23:02:23', '', 1, '2026-03-16 16:55:58'),
(12, 6, 'BONO', 100.00, '2026-02', 'PAGADO', '2026-03-07', '2026-03-16 23:02:14', '', 1, '2026-03-16 16:58:43'),
(13, 6, 'BONO', 0.10, '2026-04', 'PAGADO', '2026-03-18', '2026-03-16 23:05:02', '', 1, '2026-03-16 17:03:25'),
(14, 6, 'SALARIO', 2000.00, '2026-04', 'PAGADO', '2026-03-17', '2026-03-16 23:05:31', '', 1, '2026-03-16 17:05:28'),
(15, 3, 'BONO', 0.10, '2026-07', 'PAGADO', '2026-03-25', '2026-03-16 23:07:04', '', 1, '2026-03-16 17:07:02'),
(16, 6, 'ADELANTO', 10.00, '2026-03', 'PAGADO', '2026-03-27', '2026-03-17 04:56:14', 'Sin observaciones específicas', 1, '2026-03-16 22:49:42'),
(17, 3, 'BONO', 0.10, '2026-08', 'PAGADO', '2026-03-19', '2026-03-17 05:02:13', 'Sin observaciones específicas', 1, '2026-03-16 23:01:57'),
(18, 3, 'ADELANTO', 1111.00, '2026-08', 'PAGADO', '2026-03-17', '2026-03-17 05:03:12', 'Sin observaciones específicas', 1, '2026-03-16 23:02:43'),
(19, 3, 'BONO', 333.00, '2026-03', 'PAGADO', '2026-03-18', '2026-03-17 05:49:40', 'Sin observaciones específicas', 1, '2026-03-16 23:49:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_orden`
--

CREATE TABLE `pagos_orden` (
  `id_pago` int(11) NOT NULL,
  `id_orden` int(11) NOT NULL,
  `metodo_pago` enum('EFECTIVO','YAPE','PLIN','TARJETA') NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_orden`
--

INSERT INTO `pagos_orden` (`id_pago`, `id_orden`, `metodo_pago`, `monto`) VALUES
(1, 1, 'EFECTIVO', 300.00),
(2, 2, 'TARJETA', 300.00),
(3, 3, 'YAPE', 15.00),
(4, 4, 'EFECTIVO', 15.00),
(5, 6, 'EFECTIVO', 1.50),
(6, 5, 'EFECTIVO', 15.00),
(7, 7, 'EFECTIVO', 1.50),
(8, 8, 'EFECTIVO', 15.00),
(9, 9, 'EFECTIVO', 13.50),
(10, 10, 'EFECTIVO', 15.00),
(11, 11, 'YAPE', 1.50),
(12, 12, 'YAPE', 150.00),
(13, 13, 'YAPE', 23.50),
(14, 14, 'YAPE', 270.00),
(15, 15, 'EFECTIVO', 2.50),
(16, 16, 'EFECTIVO', 60.00),
(17, 17, 'EFECTIVO', 2.00),
(18, 18, 'EFECTIVO', 20.00),
(19, 19, 'EFECTIVO', 8.00),
(20, 20, 'EFECTIVO', 10.00),
(21, 21, 'EFECTIVO', 10.00),
(22, 23, 'YAPE', 200.00),
(23, 24, 'EFECTIVO', 10.00),
(24, 25, 'EFECTIVO', 319.00),
(25, 22, 'EFECTIVO', 200.00),
(26, 26, 'EFECTIVO', 0.00),
(27, 27, 'YAPE', 200.00),
(28, 27, '', 200.00),
(29, 28, 'YAPE', 10.00),
(30, 28, '', 10.00),
(31, 36, 'EFECTIVO', 6.00),
(32, 35, 'PLIN', 15.00),
(33, 36, '', 6.00),
(34, 34, 'EFECTIVO', 300.00),
(35, 38, 'EFECTIVO', 240.00),
(36, 37, 'EFECTIVO', 15.00),
(37, 39, 'EFECTIVO', 15.00),
(38, 38, '', 240.00),
(39, 40, 'EFECTIVO', 15.00),
(40, 41, 'EFECTIVO', 12.00),
(41, 42, 'EFECTIVO', 120.00),
(42, 43, 'EFECTIVO', 300.00),
(43, 44, 'EFECTIVO', 240.00),
(44, 45, 'EFECTIVO', 15.00),
(45, 46, 'EFECTIVO', 12.00),
(46, 47, 'EFECTIVO', 240.00),
(47, 48, 'EFECTIVO', 300.00),
(48, 49, 'EFECTIVO', 15.00),
(49, 50, 'EFECTIVO', 12.00),
(50, 51, 'EFECTIVO', 12.00),
(51, 52, 'EFECTIVO', 12.00),
(52, 53, 'EFECTIVO', 15.00),
(53, 54, 'YAPE', 120.00),
(54, 56, 'EFECTIVO', 0.00),
(55, 40, '', 15.00),
(56, 41, '', 52.00),
(57, 55, 'EFECTIVO', 111.00),
(58, 57, 'EFECTIVO', 2.00),
(59, 58, 'EFECTIVO', 1.00),
(60, 43, '', 300.00),
(61, 42, '', 120.00),
(62, 44, '', 240.00),
(63, 45, '', 15.00),
(64, 53, '', 15.00),
(65, 52, '', 12.00),
(66, 51, '', 12.00),
(67, 50, '', 12.00),
(68, 49, '', 15.00),
(69, 48, '', 300.00),
(70, 47, '', 240.00),
(71, 46, '', 12.00),
(72, 59, 'EFECTIVO', 12.00),
(73, 60, 'EFECTIVO', 12.00),
(74, 61, 'EFECTIVO', 15.00),
(75, 62, 'EFECTIVO', 10.00),
(76, 64, 'YAPE', 10.00),
(77, 66, 'EFECTIVO', 200.00),
(78, 67, 'EFECTIVO', 10.00),
(79, 68, 'PLIN', 10.00),
(80, 69, 'TARJETA', 10.00),
(81, 70, 'EFECTIVO', 200.00),
(82, 72, 'EFECTIVO', 10.00),
(83, 73, 'EFECTIVO', 10.00),
(84, 74, 'EFECTIVO', 10.00),
(85, 79, 'TARJETA', 10.00),
(86, 80, 'EFECTIVO', 10.00),
(87, 60, 'EFECTIVO', 42.00),
(88, 81, 'EFECTIVO', 10.00),
(89, 59, 'EFECTIVO', 12.00),
(90, 63, 'EFECTIVO', 200.00),
(91, 82, 'EFECTIVO', 10.00),
(92, 83, 'EFECTIVO', 10.00),
(93, 84, 'EFECTIVO', 10.00),
(94, 85, 'EFECTIVO', 10.00),
(95, 86, 'EFECTIVO', 10.00),
(96, 87, 'EFECTIVO', 10.00),
(97, 88, 'EFECTIVO', 10.00),
(98, 89, 'EFECTIVO', 10.00),
(99, 90, 'EFECTIVO', 10.00),
(100, 91, 'EFECTIVO', 10.00),
(101, 92, 'EFECTIVO', 10.00),
(102, 93, 'EFECTIVO', 10.00),
(103, 94, 'EFECTIVO', 10.00),
(104, 95, 'EFECTIVO', 10.00),
(105, 67, 'EFECTIVO', 10.00),
(106, 61, 'EFECTIVO', 15.00),
(107, 64, 'EFECTIVO', 10.00),
(108, 71, 'EFECTIVO', 10.00),
(109, 111, 'YAPE', 150.00),
(110, 116, 'YAPE', 150.00),
(111, 109, 'EFECTIVO', 30.00),
(112, 66, 'EFECTIVO', 200.00),
(113, 97, 'EFECTIVO', 0.00),
(114, 116, 'TARJETA', 150.00),
(115, 110, 'PLIN', 10.00),
(116, 111, 'TARJETA', 150.00),
(117, 121, 'EFECTIVO', 0.00),
(118, 123, 'YAPE', 10.00),
(119, 123, 'PLIN', 2.50),
(120, 96, 'EFECTIVO', 11.00),
(121, 125, 'EFECTIVO', 10.00),
(122, 126, 'EFECTIVO', 0.00),
(123, 93, 'YAPE', 1.00),
(124, 91, 'EFECTIVO', 50.00),
(125, 65, 'EFECTIVO', 10.00),
(126, 117, 'EFECTIVO', 10.00),
(127, 125, 'EFECTIVO', 20.00),
(128, 122, 'EFECTIVO', 10.00),
(129, 124, 'EFECTIVO', 14.00),
(130, 127, 'EFECTIVO', 10.00),
(131, 128, 'EFECTIVO', 38.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`id`, `id_usuario`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, '505010', '2026-03-17 14:19:21', 0, '2026-03-17 08:04:21'),
(2, 2, '910383', '2026-03-17 14:27:54', 0, '2026-03-17 08:12:54'),
(3, 2, '639684', '2026-03-17 14:28:01', 0, '2026-03-17 08:13:01'),
(4, 6, '333400', '2026-03-17 14:28:33', 0, '2026-03-17 08:13:33'),
(5, 1, '061606', '2026-03-17 14:32:35', 0, '2026-03-17 08:17:35'),
(6, 2, '778865', '2026-03-17 14:34:03', 0, '2026-03-17 08:19:03'),
(7, 2, '462443', '2026-03-17 14:35:19', 0, '2026-03-17 08:20:19'),
(8, 1, '171660', '2026-03-17 14:35:43', 0, '2026-03-17 08:20:43'),
(9, 1, '457259', '2026-03-17 14:52:26', 0, '2026-03-17 08:37:26'),
(10, 1, '601494', '2026-03-17 14:53:35', 1, '2026-03-17 08:38:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_empleados`
--

CREATE TABLE `permisos_empleados` (
  `id_permiso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo` enum('DESCANSO','PERMISO','VACACION','FALTA') NOT NULL DEFAULT 'DESCANSO',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `estado` enum('APROBADO','PENDIENTE','RECHAZADO') NOT NULL DEFAULT 'APROBADO',
  `id_admin_registrador` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_empleados`
--

INSERT INTO `permisos_empleados` (`id_permiso`, `id_usuario`, `tipo`, `fecha_inicio`, `fecha_fin`, `motivo`, `estado`, `id_admin_registrador`, `fecha_creacion`) VALUES
(1, 4, 'VACACION', '2026-03-09', '2026-03-09', '', 'APROBADO', 1, '2026-03-09 15:22:33'),
(2, 9, 'PERMISO', '2026-03-17', '2026-03-25', '', 'APROBADO', 1, '2026-03-16 16:58:05'),
(3, 6, 'DESCANSO', '2026-03-16', '2026-03-17', '', 'APROBADO', 1, '2026-03-16 17:02:58'),
(4, 3, 'FALTA', '2026-03-17', '2026-03-27', '', 'APROBADO', 1, '2026-03-16 17:07:25'),
(5, 4, 'VACACION', '2026-03-17', '2026-03-21', 'Sin motivos específicos', 'APROBADO', 1, '2026-03-16 22:49:17'),
(6, 4, 'VACACION', '2026-03-18', '2026-03-27', 'Sin motivos específicos', 'RECHAZADO', 1, '2026-03-16 23:00:20'),
(7, 7, 'VACACION', '2026-03-19', '2026-03-27', 'Sin motivos específicos', 'RECHAZADO', 1, '2026-03-16 23:02:56'),
(8, 3, 'PERMISO', '2026-03-18', '2026-03-26', 'Sin motivos específicos', 'RECHAZADO', 1, '2026-03-16 23:49:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock_actual` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 5,
  `fecha_caducidad` date DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio_compra`, `precio_venta`, `stock_actual`, `stock_minimo`, `fecha_caducidad`, `fecha_registro`) VALUES
(1, 'Galletas Rellenita', 1.00, 3.00, 12, 5, NULL, '2026-03-17 02:06:44'),
(2, 'Gaseosa', 15.00, 20.00, 101, 5, NULL, '2026-03-17 02:06:44'),
(3, 'Cigarro Laky 1', 2.00, 2.50, 94, 5, NULL, '2026-03-17 02:06:44'),
(5, 'Caramelo Limon', 12.00, 15.00, 174, 10, '2027-06-16', '2026-03-27 15:07:33'),
(6, 'Cuates', 0.80, 1.00, 13, 5, '2027-07-15', '2026-03-27 15:10:30'),
(7, 'agua', 1.00, 2.00, 4, 1, '2026-04-11', '2026-03-27 15:27:05'),
(8, 'Cerveza', 6.00, 8.00, 26, 1, '2026-04-30', '2026-03-27 16:18:10'),
(9, 'Chupetin', 1.00, 1.50, 81, 1, '2026-06-26', '2026-03-27 16:43:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_lotes`
--

CREATE TABLE `producto_lotes` (
  `id_lote` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_inicial` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` enum('ACTIVO','AGOTADO','MERMA') NOT NULL DEFAULT 'ACTIVO',
  `fecha_ingreso` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_lotes`
--

INSERT INTO `producto_lotes` (`id_lote`, `id_producto`, `cantidad_inicial`, `cantidad_actual`, `precio_compra`, `precio_venta`, `fecha_vencimiento`, `estado`, `fecha_ingreso`) VALUES
(1, 1, 26, 12, 1.00, 3.00, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(2, 2, 20, 1, 15.00, 20.00, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(3, 3, 98, 94, 2.00, 2.50, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(5, 5, 180, 174, 12.00, 15.00, '2027-06-16', 'ACTIVO', '2026-03-27 10:07:33'),
(6, 6, 25, 1, 0.80, 1.00, '2027-07-15', 'ACTIVO', '2026-03-27 10:10:30'),
(7, 7, 5, 4, 1.00, 2.00, '2026-04-11', 'ACTIVO', '2026-03-27 10:27:05'),
(8, 8, 5, 0, 6.00, 8.00, '2026-04-30', 'AGOTADO', '2026-03-27 11:18:10'),
(9, 8, 10, 0, 8.00, 10.00, '2026-04-30', 'AGOTADO', '2026-03-27 11:24:11'),
(10, 9, 5, 0, 1.00, 1.50, '2026-06-26', 'AGOTADO', '2026-03-27 11:43:13'),
(11, 9, 10, 1, 1.00, 2.00, '2026-06-26', 'ACTIVO', '2026-03-27 11:43:47'),
(12, 8, 26, 26, 6.00, 10.00, '2026-05-22', 'ACTIVO', '2026-04-14 09:51:56'),
(13, 9, 80, 80, 1.00, 1.60, '2026-06-26', 'ACTIVO', '2026-04-14 09:52:23'),
(14, 6, 12, 12, 0.80, 1.00, '2027-07-15', 'ACTIVO', '2026-04-14 09:52:33'),
(15, 2, 100, 100, 15.00, 20.00, NULL, 'ACTIVO', '2026-04-14 09:52:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_descuento` enum('PORCENTAJE','MONTO_FIJO') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `solo_una_vez_por_cliente` tinyint(1) DEFAULT 1,
  `mensaje_whatsapp` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id_promocion`, `nombre`, `tipo_descuento`, `valor`, `fecha_inicio`, `fecha_fin`, `solo_una_vez_por_cliente`, `mensaje_whatsapp`, `estado`) VALUES
(1, 'Fiestas Patrias 2025', 'PORCENTAJE', 10.00, '2026-02-03', '2026-03-17', 0, '', 0),
(2, 'San valentin', 'MONTO_FIJO', 20.00, '2026-03-12', '2026-03-24', 1, 'Hola cara de vergas', 0),
(3, 'San valentin 2', 'MONTO_FIJO', 10.00, '2026-02-12', '2026-02-27', 1, '', 0),
(4, 'prueba 1', 'MONTO_FIJO', 1.00, '2026-02-03', '2026-03-03', 1, '', 0),
(5, 'Prueba 1', 'MONTO_FIJO', 0.10, '2026-02-03', '2026-03-03', 1, '', 0),
(6, 'San valentin', 'PORCENTAJE', 10.00, '2026-03-09', '2026-03-11', 1, '', 0),
(7, 'Fin del Mundo', 'PORCENTAJE', 50.00, '2026-03-12', '2026-03-14', 1, '', 0),
(8, 'Revolución de la IA', 'PORCENTAJE', 100.00, '2026-03-12', '2026-05-01', 1, '', 1),
(9, 'Fin del Mundo', 'MONTO_FIJO', 50.00, '2026-04-07', '2026-04-14', 0, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Cajero'),
(3, 'Operario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_base` decimal(10,2) NOT NULL,
  `acumula_puntos` tinyint(1) DEFAULT 1,
  `permite_canje` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  `tiempo_estimado` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `precio_base`, `acumula_puntos`, `permite_canje`, `estado`, `tiempo_estimado`) VALUES
(1, 'premium', 200.00, 1, 1, 1, 30),
(2, 'Lavado Premium', 10.00, 1, 1, 1, 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporadas`
--

CREATE TABLE `temporadas` (
  `id_temporada` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporadas`
--

INSERT INTO `temporadas` (`id_temporada`, `nombre`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 'Temporada 1', '2026-02-02', NULL, 0),
(3, 'Temporada 2', '2026-02-03', '2026-02-03', 0),
(4, 'Verano', '2026-02-03', '2026-02-03', 0),
(5, 'Temporada 5', '2026-02-02', '2026-02-13', 0),
(6, 'Verano 2026', '2026-03-09', '2026-03-12', 0),
(7, 'Fin del Mundo ', '2026-03-12', '2026-03-26', 0),
(8, '2026', '2026-03-26', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_seguridad`
--

CREATE TABLE `tokens_seguridad` (
  `id_token` int(11) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `id_usuario_generador` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `motivo_generacion` varchar(255) DEFAULT NULL,
  `limite_usos` int(11) DEFAULT 1,
  `contador_usos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_seguridad`
--

INSERT INTO `tokens_seguridad` (`id_token`, `codigo`, `id_usuario_generador`, `fecha_creacion`, `fecha_expiracion`, `usado`, `motivo_generacion`, `limite_usos`, `contador_usos`) VALUES
(1, '0458D8', 2, '2026-03-09 12:11:22', '2026-03-09 19:11:22', 1, 'Cajero ausente - Operario cobra', 1, 0),
(2, 'D3AFBD', 2, '2026-03-09 12:15:23', '2026-03-09 19:15:23', 0, 'Otro', 1, 0),
(3, 'A1D69B', 1, '2026-03-12 02:11:08', '2026-03-12 09:11:08', 1, 'Cajero ausente - Operario cobra', 1, 0),
(4, '35E360', 1, '2026-03-16 15:01:29', '2026-03-16 22:01:29', 1, 'Cajero ausente - Operario cobra', 1, 0),
(5, '35CFBD', 1, '2026-03-16 23:59:51', '2026-03-17 06:59:51', 0, 'Cajero ausente - Operario cobra', 1, 0),
(6, '73AAA0', 1, '2026-03-17 00:01:07', '2026-03-17 07:01:07', 0, 'Corrección de registro - Cajero', 1, 0),
(7, '09BBD8', 1, '2026-03-17 00:12:10', '2026-03-17 06:17:10', 0, 'Cajero ausente - Operario cobra', 0, 0),
(8, '26BB1F', 1, '2026-04-10 15:26:54', '2026-04-10 23:26:54', 1, 'Anulación autorizada', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT 'default.png',
  `estado` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `dni`, `nombres`, `email`, `telefono`, `password_hash`, `avatar_url`, `estado`, `fecha_creacion`) VALUES
(1, 1, '00000000', 'Juanito Alcachofa', 'admin@carwash.com', '973563350', '$2y$10$AFS2GZM3nzs.1Cs6GwiUjuaN.PnxBHqdBsJQXv16024LmthPszgaq', 'default.png', 1, '2026-02-02 19:12:27'),
(2, 1, '75692933', 'Demo 1', 'demo@carwash.com', NULL, '$2y$10$gQt2B5wG1ZtJHghN9eqRFuFgOQpJTYUIQMzbPXRlWY5KTG8xmNBSe', 'default.png', 1, '2026-02-02 14:25:13'),
(3, 3, '99999999', 'Admin Test', 'operador@carwash.com', NULL, '$2y$10$ziRoElBwP2kvwTZm/TL8dOQXI5WUffQay7r3NK8PrHQxBr4RofnPq', 'default.png', 1, '2026-03-07 02:55:43'),
(4, 2, '45896555', 'Cajero 1', 'cajero@carwash.com', '973563350', '$2y$10$wmt.VHQq7pFZARVnpnyHLu8CzVzlDiuEiW89qRFMLnSlH81dHcrc.', 'default.png', 1, '2026-03-09 12:01:10'),
(5, 3, '11112222', 'Admin Subagent', 'sub@admin.com', '111111111111111', '$2y$10$eMlg8K7Y85RoJind45oYte7702.4aMXSfkK2aD2xfOHXgfo1roXe6', 'default.png', 0, '2026-03-09 18:16:27'),
(6, 3, '72256894', 'admintest@carwash.com', 'prueba@gmail.com', NULL, '$2y$10$SCWCBZrlO7BeeXKE3FdEzuBOWZU.Wq381jGJpnT9l2sPXVjla1GSO', 'default.png', 1, '2026-03-09 19:04:01'),
(7, 3, '40013213', 'ssssssss', 'prueba2@gmail.com', NULL, '$2y$10$B8gGjq7etvCa/RGf8.1iZOc29dOX/GVJJkhRbvMDh4w4sl71ex/LS', 'default.png', 0, '2026-03-09 19:05:40'),
(9, 3, '12345678', 'ssssssss', 'prueba2@gmail.com', NULL, '$2y$10$NhyLXRtlKj6719sRPDuPq.DoDo24c4PWeZ52Sr7QBuBhFFehxtCke', 'default.png', 0, '2026-03-09 19:06:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `placa` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `observaciones` text NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `id_cliente`, `id_categoria`, `placa`, `color`, `observaciones`, `fecha_registro`) VALUES
(2, 5, 4, 'A5S-8XY', 'Rojo', '', '2026-02-03 12:47:17'),
(3, 3, 4, 'GKH985', 'Rojo', '', '2026-03-09 13:17:00'),
(5, 17, 3, 'ABC123', 'azul', '', '2026-03-11 23:25:22'),
(6, 11, 2, 'GKH985', 'Rojo', '', '2026-03-16 15:09:58'),
(7, 18, 3, 'LLL356', 'Rojo', '', '2026-03-27 13:32:04'),
(8, 8, 2, 'GKH985', 'Rojo', '', '2026-03-27 14:40:41'),
(9, 3, 4, 'SSS-555', 'ssss', '', '2026-03-31 12:05:57'),
(10, 12, 3, 'AVF-544', 's', '', '2026-03-31 12:32:05'),
(11, 7, 4, 'HHH-111', '', '', '2026-03-31 12:35:06'),
(12, 9, 2, 'DDD-555', 'sdd', '', '2026-03-31 16:00:12'),
(13, 20, 2, 'AVP-544', 'Rojo', '', '2026-04-10 15:16:38');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caja_sesiones`
--
ALTER TABLE `caja_sesiones`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `categorias_vehiculos`
--
ALTER TABLE `categorias_vehiculos`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_orden` (`id_orden`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gasto`),
  ADD KEY `id_insumo_origen` (`id_insumo_origen`),
  ADD KEY `id_usuario_registrador` (`id_usuario_registrador`);

--
-- Indices de la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  ADD PRIMARY KEY (`id_historial`),
  ADD UNIQUE KEY `candado_unico` (`id_promocion`,`id_cliente`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `kardex_movimientos`
--
ALTER TABLE `kardex_movimientos`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `idx_kardex_producto` (`id_producto`),
  ADD KEY `idx_kardex_lote` (`id_lote`);

--
-- Indices de la tabla `notificaciones_recuperacion`
--
ALTER TABLE `notificaciones_recuperacion`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_temporada` (`id_temporada`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vehiculo` (`id_vehiculo`),
  ADD KEY `id_token_autorizacion` (`id_token_autorizacion`),
  ADD KEY `fk_orden_caja` (`id_caja_sesion`);

--
-- Indices de la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_admin_registrador` (`id_admin_registrador`);

--
-- Indices de la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_orden` (`id_orden`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_admin_registrador` (`id_admin_registrador`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `producto_lotes`
--
ALTER TABLE `producto_lotes`
  ADD PRIMARY KEY (`id_lote`),
  ADD KEY `idx_producto_lote` (`id_producto`,`estado`,`fecha_vencimiento`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  ADD PRIMARY KEY (`id_temporada`);

--
-- Indices de la tabla `tokens_seguridad`
--
ALTER TABLE `tokens_seguridad`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `id_usuario_generador` (`id_usuario_generador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id_vehiculo`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caja_sesiones`
--
ALTER TABLE `caja_sesiones`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `categorias_vehiculos`
--
ALTER TABLE `categorias_vehiculos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `kardex_movimientos`
--
ALTER TABLE `kardex_movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `notificaciones_recuperacion`
--
ALTER TABLE `notificaciones_recuperacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto_lotes`
--
ALTER TABLE `producto_lotes`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id_temporada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tokens_seguridad`
--
ALTER TABLE `tokens_seguridad`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja_sesiones`
--
ALTER TABLE `caja_sesiones`
  ADD CONSTRAINT `caja_sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  ADD CONSTRAINT `detalle_orden_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`),
  ADD CONSTRAINT `detalle_orden_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `detalle_orden_ibfk_3` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`id_insumo_origen`) REFERENCES `insumos` (`id_insumo`),
  ADD CONSTRAINT `gastos_ibfk_2` FOREIGN KEY (`id_usuario_registrador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  ADD CONSTRAINT `historial_uso_promociones_ibfk_1` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`),
  ADD CONSTRAINT `historial_uso_promociones_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `kardex_movimientos`
--
ALTER TABLE `kardex_movimientos`
  ADD CONSTRAINT `kardex_movimientos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones_recuperacion`
--
ALTER TABLE `notificaciones_recuperacion`
  ADD CONSTRAINT `notificaciones_recuperacion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `fk_orden_caja` FOREIGN KEY (`id_caja_sesion`) REFERENCES `caja_sesiones` (`id_sesion`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`id_temporada`) REFERENCES `temporadas` (`id_temporada`),
  ADD CONSTRAINT `ordenes_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `ordenes_ibfk_3` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  ADD CONSTRAINT `ordenes_ibfk_4` FOREIGN KEY (`id_token_autorizacion`) REFERENCES `tokens_seguridad` (`id_token`);

--
-- Filtros para la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  ADD CONSTRAINT `pagos_empleados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `pagos_empleados_ibfk_2` FOREIGN KEY (`id_admin_registrador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  ADD CONSTRAINT `pagos_orden_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`);

--
-- Filtros para la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `fk_pw_reset_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  ADD CONSTRAINT `permisos_empleados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `permisos_empleados_ibfk_2` FOREIGN KEY (`id_admin_registrador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `producto_lotes`
--
ALTER TABLE `producto_lotes`
  ADD CONSTRAINT `producto_lotes_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tokens_seguridad`
--
ALTER TABLE `tokens_seguridad`
  ADD CONSTRAINT `tokens_seguridad_ibfk_1` FOREIGN KEY (`id_usuario_generador`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_vehiculos` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
