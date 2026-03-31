-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-03-2026 a las 19:44:28
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
  `fecha_apertura` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caja_sesiones`
--

INSERT INTO `caja_sesiones` (`id_sesion`, `id_usuario`, `monto_apertura`, `monto_cierre_real`, `monto_esperado`, `diferencia`, `estado`, `fecha_apertura`, `fecha_cierre`) VALUES
(1, 4, 0.00, 0.00, 0.00, 0.00, 'CERRADA', '2026-03-26 16:18:58', '2026-03-27 09:35:58'),
(2, 4, 50.00, 100.00, 110.00, -10.00, 'CERRADA', '2026-03-27 11:26:03', '2026-03-27 12:19:14'),
(3, 4, 0.00, NULL, 0.00, NULL, 'ABIERTA', '2026-03-27 12:59:11', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias_vehiculos`
--

CREATE TABLE `categorias_vehiculos` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `factor_precio` decimal(5,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias_vehiculos`
--

INSERT INTO `categorias_vehiculos` (`id_categoria`, `nombre`, `factor_precio`) VALUES
(1, 'Auto Sedán', 1.00),
(2, 'Camioneta SUV', 1.20),
(3, 'Moto Lineal', 0.80),
(4, 'Van / Minivan', 1.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierres_caja`
--

CREATE TABLE `cierres_caja` (
  `id_cierre` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_cierre` datetime DEFAULT current_timestamp(),
  `monto_sistema` decimal(10,2) NOT NULL,
  `monto_real` decimal(10,2) NOT NULL,
  `diferencia` decimal(10,2) GENERATED ALWAYS AS (`monto_real` - `monto_sistema`) STORED,
  `comentario` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, '40013213', 'ANA MARIA ', 'MENDOZA RUPAY', 'F', '973563350', NULL, 0, 5, 0, '2026-02-02 15:24:00', 'Sin observaciones'),
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
(18, '47318373', 'SILVIO AMADOR', 'MENDOZA RUPAY', 'M', '973563350', NULL, 1, 1, 0, '2026-03-27 13:31:29', '');

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
(22, 19, 2, NULL, 1, 8.00, 8.00, NULL);

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
(3, 1, 3, '2026-03-17 01:14:19');

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
(15, 8, 9, 'VENTA', 2, 'Venta Orden #18', 18, 4, '2026-03-27 13:30:11');

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
(2, 1, '2026-03-17 08:04:23', 'PENDIENTE'),
(3, 2, '2026-03-17 08:12:56', 'ATENDIDO'),
(4, 6, '2026-03-17 08:13:35', 'ATENDIDO'),
(5, 6, '2026-03-17 08:36:59', 'PENDIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id_orden` int(11) NOT NULL,
  `id_temporada` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) DEFAULT NULL,
  `id_usuario_creador` int(11) NOT NULL,
  `id_usuario_cajero` int(11) DEFAULT NULL,
  `estado` enum('EN_COLA','EN_PROCESO','POR_COBRAR','FINALIZADO','ANULADO') DEFAULT 'EN_COLA',
  `ubicacion_en_local` varchar(50) DEFAULT NULL,
  `total_servicios` decimal(10,2) DEFAULT 0.00,
  `total_productos` decimal(10,2) DEFAULT 0.00,
  `descuento_promo` decimal(10,2) DEFAULT 0.00,
  `descuento_puntos` decimal(10,2) DEFAULT 0.00,
  `total_final` decimal(10,2) DEFAULT 0.00,
  `motivo_anulacion` varchar(255) DEFAULT NULL,
  `id_token_autorizacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `id_caja_sesion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_temporada`, `id_cliente`, `id_vehiculo`, `id_usuario_creador`, `id_usuario_cajero`, `estado`, `ubicacion_en_local`, `total_servicios`, `total_productos`, `descuento_promo`, `descuento_puntos`, `total_final`, `motivo_anulacion`, `id_token_autorizacion`, `fecha_creacion`, `fecha_cierre`, `id_caja_sesion`) VALUES
(1, 6, 9, 2, 3, 4, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, NULL, NULL, '2026-03-09 12:06:33', '2026-03-09 12:08:24', NULL),
(2, 6, 5, 2, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, NULL, 1, '2026-03-09 12:11:01', '2026-03-09 12:11:47', NULL),
(3, 6, 3, 2, 3, 3, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:32:33', '2026-03-09 12:33:18', NULL),
(4, 6, 10, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:38:29', '2026-03-09 12:40:12', NULL),
(5, 6, 8, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:43:43', '2026-03-09 12:52:37', NULL),
(6, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 12:52:31', '2026-03-09 12:52:31', NULL),
(7, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 12:52:55', '2026-03-09 12:52:55', NULL),
(8, 6, 3, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:56:41', '2026-03-09 13:02:31', NULL),
(9, 6, 3, 3, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 1.50, 0.00, 13.50, NULL, NULL, '2026-03-09 13:17:23', '2026-03-09 13:18:01', NULL),
(10, 6, 3, 3, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 13:20:45', '2026-03-09 13:20:59', NULL),
(11, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 13:39:24', '2026-03-09 13:39:24', NULL),
(12, 7, 5, 2, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 150.00, 0.00, 150.00, NULL, 3, '2026-03-12 02:10:47', '2026-03-12 02:11:25', NULL),
(13, 7, 5, 2, 3, 3, 'FINALIZADO', '', 15.00, 5.50, 0.00, 0.00, 23.50, NULL, 4, '2026-03-16 15:00:55', '2026-03-16 15:02:13', NULL),
(14, 7, 3, 3, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 30.00, 0.00, 270.00, NULL, NULL, '2026-03-17 01:14:19', '2026-03-17 01:14:53', NULL),
(15, 8, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 2.50, 0.00, 0.00, 2.50, NULL, NULL, '2026-03-26 15:38:19', '2026-03-26 15:38:19', NULL),
(16, 8, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 60.00, 0.00, 0.00, 60.00, NULL, NULL, '2026-03-27 11:26:23', '2026-03-27 11:26:23', 2),
(17, 8, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 2.00, 0.00, 0.00, 2.00, NULL, NULL, '2026-03-27 12:59:18', '2026-03-27 12:59:18', 3),
(18, 8, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 20.00, 0.00, 0.00, 20.00, NULL, NULL, '2026-03-27 13:30:11', '2026-03-27 13:30:11', 3),
(19, 8, 18, 7, 3, 3, 'FINALIZADO', '', 8.00, 0.00, 0.00, 0.00, 8.00, NULL, NULL, '2026-03-27 13:33:56', '2026-03-27 13:34:26', NULL);

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
(19, 19, 'EFECTIVO', 8.00);

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
(1, 'Galletas Rellenita', 1.00, 3.00, 26, 5, NULL, '2026-03-17 02:06:44'),
(2, 'Gaseosa', 15.00, 20.00, 20, 5, NULL, '2026-03-17 02:06:44'),
(3, 'Cigarro Laky 1', 2.00, 2.50, 98, 5, NULL, '2026-03-17 02:06:44'),
(5, 'Caramelo Limon', 12.00, 15.00, 180, 10, '2027-06-16', '2026-03-27 15:07:33'),
(6, 'Cuates', 0.80, 1.00, 25, 5, '2027-07-15', '2026-03-27 15:10:30'),
(7, 'agua', 1.00, 2.00, 5, 1, '2026-04-11', '2026-03-27 15:27:05'),
(8, 'Cerveza', 6.00, 8.00, 6, 1, '2026-04-30', '2026-03-27 16:18:10'),
(9, 'Chupetin', 1.00, 1.50, 14, 1, '2026-06-26', '2026-03-27 16:43:13');

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
(1, 1, 26, 26, 1.00, 3.00, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(2, 2, 20, 20, 15.00, 20.00, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(3, 3, 98, 98, 2.00, 2.50, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(5, 5, 180, 180, 12.00, 15.00, '2027-06-16', 'ACTIVO', '2026-03-27 10:07:33'),
(6, 6, 25, 25, 0.80, 1.00, '2027-07-15', 'ACTIVO', '2026-03-27 10:10:30'),
(7, 7, 5, 5, 1.00, 2.00, '2026-04-11', 'ACTIVO', '2026-03-27 10:27:05'),
(8, 8, 5, 0, 6.00, 8.00, '2026-04-30', 'AGOTADO', '2026-03-27 11:18:10'),
(9, 8, 10, 6, 8.00, 10.00, '2026-04-30', 'ACTIVO', '2026-03-27 11:24:11'),
(10, 9, 5, 4, 1.00, 1.50, '2026-06-26', 'ACTIVO', '2026-03-27 11:43:13'),
(11, 9, 10, 10, 1.00, 2.00, '2026-06-26', 'ACTIVO', '2026-03-27 11:43:47');

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
(1, 'Fiestas Patrias 2025', 'PORCENTAJE', 10.00, '2026-02-03', '2026-03-17', 0, '', 1),
(2, 'San valentin', 'MONTO_FIJO', 20.00, '2026-03-12', '2026-03-24', 1, 'Hola cara de vergas', 0),
(3, 'San valentin 2', 'MONTO_FIJO', 10.00, '2026-02-12', '2026-02-27', 1, '', 0),
(4, 'prueba 1', 'MONTO_FIJO', 1.00, '2026-02-03', '2026-03-03', 1, '', 0),
(5, 'Prueba 1', 'MONTO_FIJO', 0.10, '2026-02-03', '2026-03-03', 1, '', 0),
(6, 'San valentin', 'PORCENTAJE', 10.00, '2026-03-09', '2026-03-11', 1, '', 0),
(7, 'Fin del Mundo', 'PORCENTAJE', 50.00, '2026-03-12', '2026-03-14', 1, '', 0),
(8, 'Revolución de la IA', 'PORCENTAJE', 100.00, '2026-03-12', '2026-03-13', 1, '', 1);

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
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `precio_base`, `acumula_puntos`, `permite_canje`, `estado`) VALUES
(1, 'premium', 200.00, 1, 1, 1),
(2, 'Lavado Premium', 10.00, 1, 1, 1);

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
(7, '09BBD8', 1, '2026-03-17 00:12:10', '2026-03-17 06:17:10', 0, 'Cajero ausente - Operario cobra', 0, 0);

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
(1, 1, '00000000', 'Juanito Alcachofa', 'admin@carwash.com', '973563350', '$2y$10$9Oas2bmuKg/oUWfsf.Y6KOa2.lMyjsGbudJ2M/1SbjM8QoVbuEIT6', 'default.png', 1, '2026-02-02 19:12:27'),
(2, 1, '75692933', 'Demo 1', 'demo@carwash.com', NULL, '$2y$10$gQt2B5wG1ZtJHghN9eqRFuFgOQpJTYUIQMzbPXRlWY5KTG8xmNBSe', 'default.png', 1, '2026-02-02 14:25:13'),
(3, 3, '99999999', 'Admin Test', 'operador@carwash.com', NULL, '$2y$10$ziRoElBwP2kvwTZm/TL8dOQXI5WUffQay7r3NK8PrHQxBr4RofnPq', 'default.png', 1, '2026-03-07 02:55:43'),
(4, 2, '45896555', 'Cajero 1', 'cajero@carwash.com', '973563350', '$2y$10$KWhGTngEAVPtnvs7L./lkuGJX4j1Yf6fhr1csKmeWupmU0jVGG8Hi', 'default.png', 1, '2026-03-09 12:01:10'),
(5, 3, '11112222', 'Admin Subagent', 'sub@admin.com', '111111111111111', '$2y$10$eMlg8K7Y85RoJind45oYte7702.4aMXSfkK2aD2xfOHXgfo1roXe6', 'default.png', 0, '2026-03-09 18:16:27'),
(6, 3, '72256894', 'admintest@carwash.com', 'prueba@gmail.com', NULL, '$2y$10$/IoYgTJ67jtFnvqwi0njbu4Fjlsnd4NRL98Im4pjBkNRitgMGEzzC', 'default.png', 1, '2026-03-09 19:04:01'),
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
(7, 18, 3, 'LLL356', 'Rojo', '', '2026-03-27 13:32:04');

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
-- Indices de la tabla `cierres_caja`
--
ALTER TABLE `cierres_caja`
  ADD PRIMARY KEY (`id_cierre`),
  ADD KEY `id_usuario` (`id_usuario`);

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
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias_vehiculos`
--
ALTER TABLE `categorias_vehiculos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cierres_caja`
--
ALTER TABLE `cierres_caja`
  MODIFY `id_cierre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `kardex_movimientos`
--
ALTER TABLE `kardex_movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `notificaciones_recuperacion`
--
ALTER TABLE `notificaciones_recuperacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caja_sesiones`
--
ALTER TABLE `caja_sesiones`
  ADD CONSTRAINT `caja_sesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `cierres_caja`
--
ALTER TABLE `cierres_caja`
  ADD CONSTRAINT `cierres_caja_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

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
