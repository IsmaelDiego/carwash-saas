-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-04-2026 a las 19:46:08
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
(7, 4, 20.00, 58.50, 58.50, 0.00, 'CERRADA', NULL, 2, '2026-04-11 22:04:28', '2026-04-12 15:23:58'),
(8, 4, 100.00, 357.50, 357.50, 0.00, 'CERRADA', NULL, 2, '2026-04-14 10:58:06', '2026-04-14 11:00:35'),
(9, 4, 100.00, 150.00, 150.00, 0.00, 'CERRADA', NULL, 2, '2026-04-14 15:56:11', '2026-04-15 09:15:26'),
(10, 4, 100.00, 530.00, 530.00, 0.00, 'CERRADA', NULL, 2, '2026-04-15 09:55:15', '2026-04-15 10:21:49'),
(11, 4, 20.00, 20.00, 20.00, 0.00, 'CERRADA', NULL, NULL, '2026-04-15 10:40:25', '2026-04-15 10:40:44'),
(12, 4, 10.00, 10.00, 10.00, 0.00, 'CERRADA', NULL, NULL, '2026-04-15 10:47:17', '2026-04-15 10:47:43'),
(13, 4, 10.00, 10.00, 10.00, 0.00, 'CERRADA', NULL, NULL, '2026-04-15 10:49:39', '2026-04-15 10:52:55'),
(14, 4, 100.00, 102.00, 102.00, 0.00, 'CERRADA', NULL, NULL, '2026-04-15 11:42:51', '2026-04-15 11:45:46'),
(15, 4, 10.00, NULL, 10.00, NULL, 'ABIERTA', NULL, 2, '2026-04-17 11:05:00', NULL);

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
(4, 'Van / Minivan', 1.50, 1.50),
(5, 'Trailer', 2.00, 2.00);

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
(1, '00000000', 'Público General', '', NULL, NULL, NULL, 0, 0, 0, '2026-02-02 19:12:27', NULL),
(3, '40013213', 'ANA MARIA ', 'MENDOZA RUPAY', 'F', '973563350', NULL, 0, 5, 0, '2026-02-02 15:24:00', 'Sin observaciones'),
(5, '75692933', 'ISMAEL DIEGO', 'QUISPE MENDOZA', 'M', '973563350', NULL, 0, 3, 0, '2026-02-02 17:25:17', ''),
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
(18, '47318373', 'SILVIO AMADOR', 'MENDOZA RUPAY', 'M', '973563350', NULL, 0, 1, 0, '2026-03-27 13:31:29', ''),
(19, '73495857', 'NATHALIE RAQUEL', 'PRADA GUERRERO', 'F', '985632147', NULL, 0, 0, 0, '2026-04-07 16:36:38', ''),
(20, '60282375', 'YEFERSON', 'MENDOZA ARISTE', 'F', '973563350', NULL, 0, 0, 0, '2026-04-10 15:16:09', NULL),
(21, '70198965', 'ANIBAL', 'YUCRA CURO', 'M', '943524805', NULL, 1, 0, 0, '2026-04-15 11:58:12', '');

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
  `logo` varchar(255) DEFAULT 'public/img/logo.png',
  `num_rampas` int(11) NOT NULL DEFAULT 3 COMMENT 'Número total de rampas registradas por el admin',
  `cajero_puede_abrir_caja` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Cajero puede abrir su caja, 0=Solo Admin puede abrir',
  `id_operador_responsable` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_configuracion`, `nombre_negocio`, `abreviatura`, `moneda`, `modo_sin_cajero`, `meta_puntos_canje`, `logo`, `num_rampas`, `cajero_puede_abrir_caja`, `id_operador_responsable`) VALUES
(1, 'CARWASH SYSTEMS GOUDEM', 'CW-SYSTEMS', 'S/', 0, 10, 'public/uploads/logo.webp', 5, 1, NULL);

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
(1, 'Pago luz 1', 150.00, 'FIJO', '2026-03-09', NULL, 1),
(2, 'Merma: Producto vencido (Lote #7, 4 u. de 7)', 4.00, 'VARIABLE', '2026-04-14', NULL, 1),
(3, 'Merma: Producto dañado (Lote #16, 1 u. de 7)', 1.00, 'VARIABLE', '2026-04-14', NULL, 1);

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
(31, 8, 8, '2026-04-10 15:07:46'),
(33, 8, 15, '2026-04-17 11:23:31');

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

(66, 7, 20, 'ENTRADA', 1, 'Entrada Lote #20', NULL, NULL, '2026-04-14 12:52:24'),
(67, 9, 11, 'VENTA', 1, 'Venta Orden #142', 142, 4, '2026-04-15 11:45:17');

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
  `estado_pago` enum('PENDIENTE','PAGADO','PARCIAL') DEFAULT 'PENDIENTE',
  `id_rampa` int(11) DEFAULT NULL COMMENT 'Rampa asignada (cuando está EN_PROCESO)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_temporada`, `id_cliente`, `id_vehiculo`, `id_promocion`, `id_usuario_creador`, `id_usuario_cajero`, `estado`, `ubicacion_en_local`, `total_servicios`, `total_productos`, `descuento_promo`, `descuento_puntos`, `total_final`, `tiempo_total_estimado`, `motivo_anulacion`, `id_token_autorizacion`, `fecha_creacion`, `fecha_cierre`, `id_caja_sesion`, `fecha_inicio_proceso`, `fecha_fin_proceso`, `estado_pago`, `id_rampa`) VALUES

(153, 8, 3, 9, NULL, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 45, NULL, NULL, '2026-04-17 12:27:45', '2026-04-17 12:34:49', 15, '2026-04-17 12:27:45', '2026-04-17 12:34:48', 'PAGADO', 6),
(154, 8, 8, 8, 8, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 37, NULL, NULL, '2026-04-17 12:29:45', '2026-04-17 12:30:47', 15, '2026-04-17 12:29:45', '2026-04-17 12:30:44', 'PAGADO', 4),
(155, 8, 15, 16, 8, 4, NULL, 'EN_PROCESO', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 37, NULL, NULL, '2026-04-17 12:30:10', NULL, 15, '2026-04-17 12:30:44', NULL, 'PAGADO', 4),
(156, 8, 3, 9, 8, 4, 4, 'FINALIZADO', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 45, NULL, NULL, '2026-04-17 12:31:06', '2026-04-17 12:31:27', 15, '2026-04-17 12:31:19', '2026-04-17 12:31:25', 'PAGADO', NULL),
(157, 8, 8, 8, NULL, 4, NULL, 'EN_PROCESO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-17 12:32:27', NULL, NULL, '2026-04-17 12:34:48', NULL, 'PENDIENTE', 6),
(158, 8, 8, 8, NULL, 4, NULL, 'EN_PROCESO', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-17 12:34:05', NULL, 15, '2026-04-17 12:39:28', NULL, 'PAGADO', 7),
(159, 8, 15, 16, 8, 4, NULL, 'EN_COLA', NULL, 10.00, 0.00, 10.00, 0.00, 0.00, 37, NULL, NULL, '2026-04-17 12:35:05', NULL, 15, NULL, NULL, 'PAGADO', NULL),
(160, 8, 8, 8, NULL, 4, NULL, 'EN_COLA', NULL, 10.00, 0.00, 0.00, 0.00, 10.00, 37, NULL, NULL, '2026-04-17 12:44:50', NULL, 15, NULL, NULL, 'PAGADO', NULL);

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
(8, 3, 'PERMISO', '2026-03-18', '2026-03-26', 'Sin motivos específicos', 'RECHAZADO', 1, '2026-03-16 23:49:04'),
(9, 4, 'PERMISO', '2026-04-18', '2026-04-24', 'Baño', 'PENDIENTE', 4, '2026-04-17 10:17:12');

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
(2, 'Gaseosa', 15.00, 20.00, 101, 5, '2026-07-30', '2026-03-17 02:06:44'),
(3, 'Cigarro Laky 1', 2.00, 2.50, 91, 5, NULL, '2026-03-17 02:06:44'),
(5, 'Caramelo Limon', 12.00, 15.00, 174, 10, '2027-06-16', '2026-03-27 15:07:33'),
(6, 'Cuates', 0.80, 1.00, 13, 5, '2027-07-15', '2026-03-27 15:10:30'),
(7, 'agua', 1.00, 2.00, 12, 1, '2026-05-29', '2026-03-27 15:27:05'),
(8, 'Cerveza', 6.00, 8.00, 22, 1, '2026-04-30', '2026-03-27 16:18:10'),
(9, 'Chupetin', 1.00, 1.50, 80, 1, '2026-06-26', '2026-03-27 16:43:13');

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
(3, 3, 98, 91, 2.00, 2.50, NULL, 'ACTIVO', '2026-03-27 10:02:41'),
(5, 5, 180, 174, 12.00, 15.00, '2027-06-16', 'ACTIVO', '2026-03-27 10:07:33'),
(6, 6, 25, 1, 0.80, 1.00, '2027-07-15', 'ACTIVO', '2026-03-27 10:10:30'),
(7, 7, 5, 0, 1.00, 2.00, '2026-04-11', 'MERMA', '2026-03-27 10:27:05'),
(8, 8, 5, 0, 6.00, 8.00, '2026-04-30', 'AGOTADO', '2026-03-27 11:18:10'),
(9, 8, 10, 0, 8.00, 10.00, '2026-04-30', 'AGOTADO', '2026-03-27 11:24:11'),
(10, 9, 5, 0, 1.00, 1.50, '2026-06-26', 'AGOTADO', '2026-03-27 11:43:13'),
(11, 9, 10, 0, 1.00, 2.00, '2026-06-26', 'AGOTADO', '2026-03-27 11:43:47'),
(12, 8, 26, 22, 6.00, 10.00, '2026-05-22', 'ACTIVO', '2026-04-14 09:51:56'),
(13, 9, 80, 80, 1.00, 1.60, '2026-06-26', 'ACTIVO', '2026-04-14 09:52:23'),
(14, 6, 12, 12, 0.80, 1.00, '2027-07-15', 'ACTIVO', '2026-04-14 09:52:33'),
(15, 2, 100, 100, 15.00, 20.00, NULL, 'ACTIVO', '2026-04-14 09:52:42'),
(16, 7, 1, 0, 1.00, 2.00, '2026-04-30', 'MERMA', '2026-04-14 12:32:35'),
(17, 7, 1, 0, 1.00, 2.00, '2026-04-30', 'AGOTADO', '2026-04-14 12:48:59'),
(18, 7, 10, 10, 1.00, 2.00, '2026-07-24', 'ACTIVO', '2026-04-14 12:50:05'),
(19, 7, 1, 1, 1.00, 2.00, '2026-05-29', 'ACTIVO', '2026-04-14 12:50:45'),
(20, 7, 1, 1, 1.00, 2.00, '2026-05-29', 'ACTIVO', '2026-04-14 12:52:24');

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
(9, 'Fin del Mundo', 'MONTO_FIJO', 50.00, '2026-04-07', '2026-04-14', 0, '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rampas`
--

CREATE TABLE `rampas` (
  `id_rampa` int(11) NOT NULL,
  `numero` int(11) NOT NULL COMMENT 'Número de rampa (1, 2, 3...)',
  `nombre` varchar(50) NOT NULL DEFAULT '' COMMENT 'Nombre o alias de la rampa',
  `id_operador` int(11) DEFAULT NULL COMMENT 'Operario asignado',
  `estado` enum('ACTIVA','INACTIVA','DESCANSO') NOT NULL DEFAULT 'ACTIVA' COMMENT 'ACTIVA=disponible, INACTIVA=sin personal, DESCANSO=break',
  `motivo_estado` varchar(100) DEFAULT NULL COMMENT 'Motivo de inactivación o descanso',
  `fecha_actualizacion` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rampas`
--

INSERT INTO `rampas` (`id_rampa`, `numero`, `nombre`, `id_operador`, `estado`, `motivo_estado`, `fecha_actualizacion`) VALUES
(4, 1, 'Rampa 1', 3, 'ACTIVA', 'Falta de personal', '2026-04-15 09:54:49'),
(5, 2, 'Rampa 2', 6, 'ACTIVA', 'Falta de personal', '2026-04-15 09:56:44'),
(6, 3, 'Rampa 3', 6, 'ACTIVA', 'Falta de personal', '2026-04-17 11:22:10'),
(7, 4, 'Rampa 4', 6, 'ACTIVA', 'Falta de personal', '2026-04-17 12:41:13'),
(8, 5, 'Rampa 5', NULL, 'INACTIVA', NULL, '2026-04-15 11:43:24');

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
-- Estructura de tabla para la tabla `solicitudes_caja`
--

CREATE TABLE `solicitudes_caja` (
  `id_solicitud` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'Cajero o encargado que lo solicita',
  `monto_sugerido` decimal(10,2) DEFAULT 0.00,
  `estado` enum('PENDIENTE','APROBADA','RECHAZADA') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_caja`
--

INSERT INTO `solicitudes_caja` (`id_solicitud`, `id_usuario`, `monto_sugerido`, `estado`, `fecha_solicitud`, `fecha_respuesta`) VALUES
(1, 4, 0.00, 'APROBADA', '2026-04-15 10:28:33', '2026-04-15 10:40:25'),
(2, 4, 0.00, 'APROBADA', '2026-04-15 10:45:25', '2026-04-15 10:47:17'),
(3, 4, 0.00, 'APROBADA', '2026-04-15 10:48:12', '2026-04-15 10:49:39'),
(4, 4, 0.00, 'APROBADA', '2026-04-15 11:42:37', '2026-04-15 11:42:51');

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
(8, '26BB1F', 1, '2026-04-10 15:26:54', '2026-04-10 23:26:54', 1, 'Anulación autorizada', 1, 1),
(9, 'AD8B06', 1, '2026-04-15 11:47:31', '2026-04-15 19:47:31', 0, 'Cajero ausente - Operario cobra', 1, 0);

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
(5, 3, '11112222', 'Admin Subagent', 'sub@admin.com', '111111111111111', '$2y$10$eMlg8K7Y85RoJind45oYte7702.4aMXSfkK2aD2xfOHXgfo1roXe6', 'default.png', 1, '2026-03-09 18:16:27'),
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
(13, 20, 2, 'AVP-544', 'Rojo', '', '2026-04-10 15:16:38'),
(14, 3, 1, 'AVF-544', 'sdd', '', '2026-04-14 15:58:31'),
(15, 15, 2, 'DDD-555', 'Rojo', '', '2026-04-14 15:59:39'),
(16, 15, 2, 'DDD-555', 'Rojo', '', '2026-04-14 15:59:44');

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
  ADD KEY `fk_orden_caja` (`id_caja_sesion`),
  ADD KEY `fk_orden_rampa` (`id_rampa`);

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
-- Indices de la tabla `rampas`
--
ALTER TABLE `rampas`
  ADD PRIMARY KEY (`id_rampa`),
  ADD KEY `id_operador` (`id_operador`);

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
-- Indices de la tabla `solicitudes_caja`
--
ALTER TABLE `solicitudes_caja`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_usuario` (`id_usuario`);

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
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `categorias_vehiculos`
--
ALTER TABLE `categorias_vehiculos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `kardex_movimientos`
--
ALTER TABLE `kardex_movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `notificaciones_recuperacion`
--
ALTER TABLE `notificaciones_recuperacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT de la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto_lotes`
--
ALTER TABLE `producto_lotes`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rampas`
--
ALTER TABLE `rampas`
  MODIFY `id_rampa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT de la tabla `solicitudes_caja`
--
ALTER TABLE `solicitudes_caja`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id_temporada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tokens_seguridad`
--
ALTER TABLE `tokens_seguridad`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `fk_orden_rampa` FOREIGN KEY (`id_rampa`) REFERENCES `rampas` (`id_rampa`) ON DELETE SET NULL,
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
-- Filtros para la tabla `rampas`
--
ALTER TABLE `rampas`
  ADD CONSTRAINT `rampas_ibfk_1` FOREIGN KEY (`id_operador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `solicitudes_caja`
--
ALTER TABLE `solicitudes_caja`
  ADD CONSTRAINT `fk_solicitud_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

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
