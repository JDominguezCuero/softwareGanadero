-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-05-2025 a las 05:33:51
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
-- Base de datos: `ganaderiasimulador`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--
CREATE DATABASE ganaderiasimulador;
USE ganaderiasimulador;

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo_actividad` enum('Alimentación','Vacunación','Manejo','Reproducción') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales`
--

CREATE TABLE `animales` (
  `id_animal` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `estado` enum('Activo','Enfermo','Muerto') DEFAULT 'Activo',
  `fecha_ingreso` date DEFAULT NULL,
  `alimentacion` int(11) DEFAULT 100,
  `higiene` int(11) DEFAULT 100,
  `salud` int(11) DEFAULT 100,
  `produccion` int(11) DEFAULT 0,
  `id_usuario` int(11) NOT NULL,
  `last_updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `animales`
--

INSERT INTO `animales` (`id_animal`, `nombre`, `id_tipo`, `edad`, `peso`, `estado`, `fecha_ingreso`, `alimentacion`, `higiene`, `salud`, `produccion`, `id_usuario`, `last_updated_at`) VALUES
(109, 'Sds - 1', 2, 2, NULL, 'Activo', NULL, 100, 100, 100, 0, 7, '2025-05-24 18:37:15'),
(112, 'Sds - 3', 2, 2, NULL, 'Activo', NULL, 100, 100, 100, 0, 7, '2025-05-24 18:37:15'),
(130, 'Lola - 1', 1, 2, NULL, 'Activo', NULL, 100, 100, 100, 0, 4, '2025-05-24 18:37:15'),
(131, 'saasas', 1, 2, NULL, 'Activo', NULL, 100, 100, 100, 0, 4, '2025-05-24 18:37:15'),
(158, 'Sara - 2', 2, 3, NULL, 'Activo', NULL, 69, 89, 100, 0, 5, '2025-05-25 22:30:10'),
(161, 'ddddDd - 5', 4, 3, NULL, 'Activo', NULL, 0, 0, 0, 0, 5, '2025-05-25 22:23:40'),
(164, 'E - 7', 1, 2, NULL, 'Activo', NULL, 0, 0, 0, 0, 5, '2025-05-25 22:23:40'),
(166, 'D - 9', 5, 3, NULL, 'Activo', NULL, 20, 20, 15, 0, 5, '2025-05-25 22:25:18'),
(167, 'R d - 5', 4, 1, NULL, 'Activo', NULL, 0, 0, 0, 0, 5, '2025-05-25 22:23:40'),
(168, '1 - 6', 7, 1, NULL, 'Activo', NULL, 0, 0, 0, 0, 5, '2025-05-25 22:23:40'),
(169, '1 - 7', 6, 1, NULL, 'Activo', NULL, 0, 0, 0, 0, 5, '2025-05-25 22:23:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarioalimentos`
--

CREATE TABLE `inventarioalimentos` (
  `id_alimento` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida` enum('Kg','L') DEFAULT 'Kg',
  `fecha_ingreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logsactividades`
--

CREATE TABLE `logsactividades` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `actividad` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logsactividades`
--

INSERT INTO `logsactividades` (`id_log`, `id_usuario`, `actividad`, `fecha`) VALUES
(1, 1, 'Accedió al sistema', '2025-04-28 07:29:50'),
(2, 2, 'Registró una venta de productos', '2025-04-28 07:29:50'),
(3, 3, 'Consultó reportes de animales', '2025-04-28 07:29:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL,
  `nombre_permiso` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `descripcion`) VALUES
(1, 'Gestionar Productos', 'Crear, editar y eliminar productos ganaderos'),
(2, 'Gestionar Ventas', 'Registrar y ver ventas de productos'),
(3, 'Gestionar Usuarios', 'Crear, editar y eliminar usuarios'),
(4, 'Ver Reportes', 'Acceso a reportes de gestión');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productosganaderos`
--

CREATE TABLE `productosganaderos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad_medida` enum('Litros','Kg') NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `fecha_venta` date DEFAULT NULL,
  `descripcion_producto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productosganaderos`
--

INSERT INTO `productosganaderos` (`id_producto`, `nombre_producto`, `cantidad`, `unidad_medida`, `precio_unitario`, `fecha_venta`, `descripcion_producto`) VALUES
(1, 'Leche de vaca', 22323, 'Litros', 2.56, NULL, 'Litro de leche fresca de ganado'),
(2, 'Carne de res', 111111, 'Litros', 8.00, NULL, 'Kilogramo de carne de alta calidad'),
(3, 'Queso artesanal', 6, 'Litros', 5.00, NULL, 'Queso producido de manera tradicional');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reproducciones`
--

CREATE TABLE `reproducciones` (
  `id_reproduccion` int(11) NOT NULL,
  `id_animal_macho` int(11) NOT NULL,
  `id_animal_hembra` int(11) NOT NULL,
  `fecha_reproduccion` date DEFAULT NULL,
  `fecha_nacimiento_cria` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Empleado', 'Acceso limitado para gestionar ventas y productos'),
(3, 'Veterinario', 'Acceso a la gestión de salud animal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rolespermisos`
--

CREATE TABLE `rolespermisos` (
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rolespermisos`
--

INSERT INTO `rolespermisos` (`id_rol`, `id_permiso`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 2),
(3, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stockalimentos`
--

CREATE TABLE `stockalimentos` (
  `id_stock` int(11) NOT NULL,
  `id_alimento` int(11) NOT NULL,
  `cantidad_utilizada` decimal(10,2) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposanimales`
--

CREATE TABLE `tiposanimales` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposanimales`
--

INSERT INTO `tiposanimales` (`id_tipo`, `nombre`, `descripcion`) VALUES
(1, 'Vaca', NULL),
(2, 'Cerdo', NULL),
(3, 'Gallina', NULL),
(4, 'Cabra', NULL),
(5, 'Toro', NULL),
(6, 'Burro', NULL),
(7, 'Caballo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id_transaccion` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `tipo_transaccion` enum('Compra','Venta') NOT NULL,
  `fecha` date DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `comprador_vendedor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE `tratamientos` (
  `id_tratamiento` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `tipo_tratamiento` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `correo_usuario` varchar(100) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_ultimo_acceso` timestamp NULL DEFAULT NULL,
  `nombreCompleto` varchar(255) DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `correo_usuario`, `contrasena_usuario`, `id_rol`, `estado`, `fecha_creacion`, `fecha_ultimo_acceso`, `nombreCompleto`, `token_recuperacion`, `token_expiracion`) VALUES
(1, 'admin', 'admin@example.com', 'hashed_password_admin', 1, 'Activo', '2025-04-28 07:27:02', NULL, NULL, NULL, NULL),
(2, 'empleado1', 'empleado1@example.com', 'hashed_password_empleado', 2, 'Activo', '2025-04-28 07:27:02', NULL, NULL, NULL, NULL),
(3, 'veterinario1', 'veterinario1@example.com', 'hashed_password_veterinario', 3, 'Activo', '2025-04-28 07:27:02', NULL, NULL, NULL, NULL),
(4, 'JoseADMIN', 'jsdmngzc@gmail.com', '$2y$10$vQgXsCu2HIeaXzpoO15/GukEkCZZNPL28cJvyWzdSkhxKygefPhbO', 2, 'Activo', '2025-04-28 08:14:35', NULL, 'Jose Dominguez', '8273b79dad1d460261214b38d8c80a56', '2025-05-23 17:14:02'),
(5, 'JoseD1', 'jose12@gmail.com', '$2y$10$buvEGnSzBEr4MK3ykj3TTusKzNXqy59KD79XqcDrWAVeSNBCXR8DO', 1, 'Activo', '2025-05-16 03:34:19', NULL, 'Jose Dominguez Cuero', NULL, NULL),
(6, 'JoseDom2', 'jdominguez1@gmail.com', '$2y$10$Iy2f0l0SOD6nDmdTSKbJl.a5Y3qEu9z0d/FZZti.7wR23DEaneOkK', 1, 'Activo', '2025-05-18 19:18:59', NULL, 'Jose Dominguez Cuero', NULL, NULL),
(7, 'JhoelG2', 'josedominguez.121398@gmail.com', '$2y$10$vRHLNwfPCxjAf.wE3RBSIeW/MP6nI0PNlu1Hq8zwzBUKsmJjXtnDG', 1, 'Activo', '2025-05-18 19:46:22', NULL, 'Jose Dominguez Cuero', '69c45e18c265bddff979295308dba5d6', '2025-05-22 04:24:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `fecha_venta` date DEFAULT NULL,
  `total_venta` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventasproductos`
--

CREATE TABLE `ventasproductos` (
  `id_venta_producto` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total_venta` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED,
  `fecha_venta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventasproductos`
--

INSERT INTO `ventasproductos` (`id_venta_producto`, `id_producto`, `cantidad`, `precio_unitario`, `fecha_venta`) VALUES
(1, 1, 10, 2.50, '2025-04-28'),
(2, 2, 5, 8.00, '2025-04-27'),
(3, 3, 7, 5.00, '2025-04-26');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `animales`
--
ALTER TABLE `animales`
  ADD PRIMARY KEY (`id_animal`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `fk_animales_usuarios` (`id_usuario`);

--
-- Indices de la tabla `inventarioalimentos`
--
ALTER TABLE `inventarioalimentos`
  ADD PRIMARY KEY (`id_alimento`);

--
-- Indices de la tabla `logsactividades`
--
ALTER TABLE `logsactividades`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `productosganaderos`
--
ALTER TABLE `productosganaderos`
  ADD PRIMARY KEY (`id_producto`);

--
-- Indices de la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  ADD PRIMARY KEY (`id_reproduccion`),
  ADD KEY `id_animal_macho` (`id_animal_macho`),
  ADD KEY `id_animal_hembra` (`id_animal_hembra`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `stockalimentos`
--
ALTER TABLE `stockalimentos`
  ADD PRIMARY KEY (`id_stock`),
  ADD KEY `id_alimento` (`id_alimento`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `tiposanimales`
--
ALTER TABLE `tiposanimales`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id_transaccion`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD PRIMARY KEY (`id_tratamiento`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo_usuario` (`correo_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `ventasproductos`
--
ALTER TABLE `ventasproductos`
  ADD PRIMARY KEY (`id_venta_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `animales`
--
ALTER TABLE `animales`
  MODIFY `id_animal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT de la tabla `inventarioalimentos`
--
ALTER TABLE `inventarioalimentos`
  MODIFY `id_alimento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logsactividades`
--
ALTER TABLE `logsactividades`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productosganaderos`
--
ALTER TABLE `productosganaderos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  MODIFY `id_reproduccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `stockalimentos`
--
ALTER TABLE `stockalimentos`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tiposanimales`
--
ALTER TABLE `tiposanimales`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id_transaccion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  MODIFY `id_tratamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventasproductos`
--
ALTER TABLE `ventasproductos`
  MODIFY `id_venta_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `animales`
--
ALTER TABLE `animales`
  ADD CONSTRAINT `animales_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tiposanimales` (`id_tipo`),
  ADD CONSTRAINT `fk_animales_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logsactividades`
--
ALTER TABLE `logsactividades`
  ADD CONSTRAINT `logsactividades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  ADD CONSTRAINT `reproducciones_ibfk_1` FOREIGN KEY (`id_animal_macho`) REFERENCES `animales` (`id_animal`),
  ADD CONSTRAINT `reproducciones_ibfk_2` FOREIGN KEY (`id_animal_hembra`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `rolespermisos`
--
ALTER TABLE `rolespermisos`
  ADD CONSTRAINT `rolespermisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  ADD CONSTRAINT `rolespermisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`);

--
-- Filtros para la tabla `stockalimentos`
--
ALTER TABLE `stockalimentos`
  ADD CONSTRAINT `stockalimentos_ibfk_1` FOREIGN KEY (`id_alimento`) REFERENCES `inventarioalimentos` (`id_alimento`),
  ADD CONSTRAINT `stockalimentos_ibfk_2` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD CONSTRAINT `transacciones_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  ADD CONSTRAINT `tratamientos_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `ventasproductos`
--
ALTER TABLE `ventasproductos`
  ADD CONSTRAINT `ventasproductos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productosganaderos` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
