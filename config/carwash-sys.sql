-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-03-2026 a las 21:52:50
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
(3, '40013213', 'ANA MARIA ', 'MENDOZA RUPAY', 'F', '973563350', NULL, 0, 2, 0, '2026-02-02 15:24:00', 'Sin observaciones'),
(5, '75692933', 'ISMAEL DIEGO', 'QUISPE MENDOZA', 'M', '973563350', NULL, 0, 0, 0, '2026-02-02 17:25:17', ''),
(7, '23394629', 'CONSTANTINO', 'MENDOZA FLORES', 'M', '973563350', NULL, 0, 0, 0, '2026-02-02 18:46:48', ''),
(8, '71875931', 'CAMILO ANTHONY', 'HUAYHUA CASTAÑEDA', 'M', '935 651 231', NULL, 0, 0, 0, '2026-02-03 17:22:15', ''),
(9, '17903382', 'CESAR', 'ACUÑA PERALTA', NULL, ' 973 596 626', NULL, 1, 0, 0, '2026-02-03 17:36:09', ''),
(10, '74589658', 'YENY', 'UCHARO OCHOA', 'M', '931 993 019', NULL, 0, 0, 0, '2026-02-03 17:36:56', ''),
(11, '72356894', 'IRMA KIARA', 'MORAN MICHILOT', 'M', '921 519 221', NULL, 0, 0, 0, '2026-02-03 17:38:04', ''),
(12, '73324115', 'MARCOS ROBERTO', 'PECHO LEANDRO', 'M', '906 829 934', NULL, 0, 0, 0, '2026-02-03 17:39:05', ''),
(13, '74236698', 'SUZETTI BELEN', 'QUISPE TAYPICAHUANA', 'M', '942 139 121', NULL, 0, 0, 0, '2026-02-03 17:41:05', ''),
(14, '74589634', 'LESLY HELEN', 'VILCHEZ SUERE', 'F', '973563350', NULL, 1, 0, 0, '2026-03-09 13:26:33', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL,
  `nombre_negocio` varchar(100) NOT NULL DEFAULT 'Mi Carwash',
  `abreviatura` varchar(10) DEFAULT 'CW',
  `moneda` varchar(5) DEFAULT 'S/',
  `logo_url` varchar(255) DEFAULT NULL,
  `modo_sin_cajero` tinyint(1) DEFAULT 0,
  `meta_puntos_canje` int(11) DEFAULT 10,
  `logo` varchar(255) DEFAULT 'public/img/logo.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id_configuracion`, `nombre_negocio`, `abreviatura`, `moneda`, `logo_url`, `modo_sin_cajero`, `meta_puntos_canje`, `logo`) VALUES
(1, 'Carwash XP', 'CW', 'S/', NULL, 0, 10, 'public/uploads/1773084912_Captura de pantalla 2026-02-03 194956.png');

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
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_orden`
--

INSERT INTO `detalle_orden` (`id_detalle`, `id_orden`, `id_servicio`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, NULL, 1, 300.00, 300.00),
(2, 2, 1, NULL, 1, 300.00, 300.00),
(3, 3, 2, NULL, 1, 15.00, 15.00),
(4, 4, 2, NULL, 1, 15.00, 15.00),
(5, 5, 2, NULL, 1, 15.00, 15.00),
(6, 6, NULL, 1, 1, 1.50, 1.50),
(7, 7, NULL, 1, 1, 1.50, 1.50),
(8, 8, 2, NULL, 1, 15.00, 15.00),
(9, 9, 2, NULL, 1, 15.00, 15.00),
(10, 10, 2, NULL, 1, 15.00, 15.00),
(11, 11, NULL, 1, 1, 1.50, 1.50);

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
(1, 6, 3, '2026-03-09 13:17:23');

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
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_temporada`, `id_cliente`, `id_vehiculo`, `id_usuario_creador`, `id_usuario_cajero`, `estado`, `ubicacion_en_local`, `total_servicios`, `total_productos`, `descuento_promo`, `descuento_puntos`, `total_final`, `motivo_anulacion`, `id_token_autorizacion`, `fecha_creacion`, `fecha_cierre`) VALUES
(1, 6, 9, 2, 3, 4, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, NULL, NULL, '2026-03-09 12:06:33', '2026-03-09 12:08:24'),
(2, 6, 5, 2, 3, 3, 'FINALIZADO', '', 300.00, 0.00, 0.00, 0.00, 300.00, NULL, 1, '2026-03-09 12:11:01', '2026-03-09 12:11:47'),
(3, 6, 3, 2, 3, 3, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:32:33', '2026-03-09 12:33:18'),
(4, 6, 10, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:38:29', '2026-03-09 12:40:12'),
(5, 6, 8, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:43:43', '2026-03-09 12:52:37'),
(6, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 12:52:31', '2026-03-09 12:52:31'),
(7, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 12:52:55', '2026-03-09 12:52:55'),
(8, 6, 3, 2, 3, 4, 'FINALIZADO', '', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 12:56:41', '2026-03-09 13:02:31'),
(9, 6, 3, 3, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 1.50, 0.00, 13.50, NULL, NULL, '2026-03-09 13:17:23', '2026-03-09 13:18:01'),
(10, 6, 3, 3, 3, 4, 'FINALIZADO', 'ssss', 15.00, 0.00, 0.00, 0.00, 15.00, NULL, NULL, '2026-03-09 13:20:45', '2026-03-09 13:20:59'),
(11, 6, 1, NULL, 4, 4, 'FINALIZADO', 'Venta Directa', 0.00, 1.50, 0.00, 0.00, 1.50, NULL, NULL, '2026-03-09 13:39:24', '2026-03-09 13:39:24');

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
(8, 4, 'ADELANTO', 2000.00, '2026-04', 'PAGADO', '2026-03-09', '2026-03-09 21:19:25', '', 2, '2026-03-09 15:19:25');

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
(11, 11, 'YAPE', 1.50);

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
(1, 4, 'VACACION', '2026-03-09', '2026-03-09', '', 'PENDIENTE', 2, '2026-03-09 15:22:33');

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
  `stock_minimo` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio_compra`, `precio_venta`, `stock_actual`, `stock_minimo`) VALUES
(1, 'Galletas Rellenita', 1.00, 1.50, 27, 5);

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
(1, 'Fiestas Patrias 2025', 'PORCENTAJE', 10.00, '2026-02-03', '2026-02-04', 1, NULL, 0),
(2, 'San valentin', 'MONTO_FIJO', 20.00, '2026-02-27', '2026-03-24', 1, 'Hola cara de vergas', 0),
(3, 'San valentin 2', 'MONTO_FIJO', 10.00, '2026-02-12', '2026-02-27', 1, '', 0),
(4, 'prueba 1', 'MONTO_FIJO', 1.00, '2026-02-03', '2026-03-03', 1, '', 0),
(5, 'Prueba 1', 'MONTO_FIJO', 0.10, '2026-02-03', '2026-03-03', 1, '', 0),
(6, 'San valentin', 'PORCENTAJE', 10.00, '2026-03-09', '2026-03-11', 1, '', 1);

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
(6, 'Verano 2026', '2026-03-09', NULL, 1);

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
  `motivo_generacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_seguridad`
--

INSERT INTO `tokens_seguridad` (`id_token`, `codigo`, `id_usuario_generador`, `fecha_creacion`, `fecha_expiracion`, `usado`, `motivo_generacion`) VALUES
(1, '0458D8', 2, '2026-03-09 12:11:22', '2026-03-09 19:11:22', 1, 'Cajero ausente - Operario cobra'),
(2, 'D3AFBD', 2, '2026-03-09 12:15:23', '2026-03-09 19:15:23', 0, 'Otro');

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
(1, 1, '00000000', 'Super Admin', 'admin@carwash.com', NULL, '$2y$10$hw6qpMPaUG9NE0Iq2HXVjOjNOb5I75P8ojkIb3gUgYAQJq82sD7jS', 'default.png', 1, '2026-02-02 19:12:27'),
(2, 1, '75692933', 'Ismael Diego 1', 'demo@carwash.com', NULL, '$2y$10$5CEYiiQI2fhbUhh6ubksleny82C2n15eWghvUz5TsF9zyZqZqTu5a', 'default.png', 1, '2026-02-02 14:25:13'),
(3, 3, '99999999', 'Admin Test', 'operador@carwash.com', NULL, '$2y$10$42zxfCi3DCVYqm.AQok6dOhD.BTBzkBhyQPNoA37GNg7JkCm2dnZK', 'default.png', 1, '2026-03-07 02:55:43'),
(4, 2, '45896555', 'Cajero 1', 'cajero@carwash.com', '973563350', '$2y$10$KWhGTngEAVPtnvs7L./lkuGJX4j1Yf6fhr1csKmeWupmU0jVGG8Hi', 'default.png', 1, '2026-03-09 12:01:10');

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
(3, 3, 4, 'GKH985', 'Rojo', '', '2026-03-09 13:17:00');

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_temporada` (`id_temporada`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vehiculo` (`id_vehiculo`),
  ADD KEY `id_token_autorizacion` (`id_token_autorizacion`);

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
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_orden`
--
ALTER TABLE `detalle_orden`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gasto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_uso_promociones`
--
ALTER TABLE `historial_uso_promociones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `insumos`
--
ALTER TABLE `insumos`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pagos_empleados`
--
ALTER TABLE `pagos_empleados`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pagos_orden`
--
ALTER TABLE `pagos_orden`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `temporadas`
--
ALTER TABLE `temporadas`
  MODIFY `id_temporada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tokens_seguridad`
--
ALTER TABLE `tokens_seguridad`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

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
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
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
-- Filtros para la tabla `permisos_empleados`
--
ALTER TABLE `permisos_empleados`
  ADD CONSTRAINT `permisos_empleados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `permisos_empleados_ibfk_2` FOREIGN KEY (`id_admin_registrador`) REFERENCES `usuarios` (`id_usuario`);

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
