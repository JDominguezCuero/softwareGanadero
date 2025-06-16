-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2025 a las 06:56:08
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

DROP DATABASE IF EXISTS ganaderiasimulador;
CREATE DATABASE ganaderiasimulador;
USE ganaderiasimulador;

CREATE TABLE `actividades` (
  `id_actividad` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `tipo_actividad` enum('Tipo1','Tipo2','Tipo3') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `descripcion`, `fecha`, `id_animal`, `tipo_actividad`) VALUES
(1, 'Chequeo de rutina y ordeño', '2025-05-01', 1, 'Tipo1'),
(2, 'Vacunación anual', '2025-04-20', 2, 'Tipo2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales`
--

CREATE TABLE `animales` (
  `id_animal` int(11) NOT NULL,
  `id_tipo` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `peso` decimal(10,2) DEFAULT NULL,
  `produccion` int(11) DEFAULT NULL,
  `alimentacion` int(11) DEFAULT NULL,
  `higiene` int(11) DEFAULT NULL,
  `salud` int(11) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Otro') DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `last_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `animales`
--

INSERT INTO `animales` (`id_animal`, `id_tipo`, `id_usuario`, `nombre`, `edad`, `peso`, `produccion`, `alimentacion`, `higiene`, `salud`, `estado`, `fecha_ingreso`, `last_updated_at`) VALUES
(1, 1, 1, 'Lola', 4, 550.00, 20, 90, 85, 95, 'Activo', '2023-01-15', '2025-06-02 22:04:54'),
(2, 2, 1, 'Toro Fuerte', 5, 800.00, 0, 95, 80, 90, 'Activo', '2022-03-20', '2025-06-02 22:04:54'),
(3, 7, 2, 'Pío', 1, 2.50, 1, 90, 95, 98, 'Activo', '2024-02-10', '2025-06-02 22:04:54'),
(4, 3, 3, 'Rocinante', 7, 300.00, 0, 80, 70, 85, 'Activo', '2021-06-01', '2025-06-02 22:04:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanas`
--

CREATE TABLE `campanas` (
  `id_campana` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('activa','inactivo') DEFAULT NULL,
  `fecha_evento` datetime NOT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `ubicacion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campanas`
--

INSERT INTO `campanas` (`id_campana`, `descripcion`, `estado`, `fecha_evento`, `fecha_publicacion`, `id_usuario`, `imagen_url`, `titulo`, `ubicacion`) VALUES
(1, 'Gran exposición de ganado y productos agrícolas', 'activa', '2025-07-20 10:00:00', '2025-06-02 22:04:55', 1, '/LoginADSO/public/assets/images/placeholder_campana.png', 'Feria Ganadera 2025', 'Recinto Ferial de Boyacá');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriasproducto`
--

CREATE TABLE `categoriasproducto` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoriasproducto`
--

INSERT INTO `categoriasproducto` (`id_categoria`, `nombre_categoria`) VALUES
(5, 'Alimentos para Animales'),
(4, 'Carnes y Embutidos'),
(6, 'Equipos y Suministros'),
(3, 'Lácteos y Huevos'),
(1, 'Ofertas'),
(2, 'Productos Frescos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallespedido`
--

CREATE TABLE `detallespedido` (
  `id_detalle_pedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarioalimentos`
--

CREATE TABLE `inventarioalimentos` (
  `id_alimento` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(255) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventarioalimentos`
--

INSERT INTO `inventarioalimentos` (`id_alimento`, `nombre`, `cantidad`, `unidad_medida`, `fecha_ingreso`) VALUES
(1, 'Pasto Seco', 1000.00, 'Kg', '2025-01-01'),
(2, 'Maíz Molido', 500.00, 'Kg', '2025-02-15'),
(4, 'cccs', 3.00, '', '2025-06-02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logsactividades`
--

CREATE TABLE `logsactividades` (
  `id_log` int(11) NOT NULL,
  `actividad` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logsactividades`
--

INSERT INTO `logsactividades` (`id_log`, `actividad`, `fecha`, `id_usuario`) VALUES
(1, 'Accedió al sistema y actualizó precios de productos', '2025-06-03 03:04:54', 1),
(2, 'Registró una venta de productos al cliente X', '2025-06-03 03:04:54', 2),
(3, 'Consultó reportes de salud animal para el ganado', '2025-06-03 03:04:54', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `direccion_envio` text NOT NULL,
  `estado_pedido` varchar(50) DEFAULT 'Pendiente',
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `total_pedido` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 'Ver Reportes', 'Acceso a reportes de gestión'),
(5, 'Gestionar Animales', 'Registrar y actualizar información de animales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productosganaderos`
--

CREATE TABLE `productosganaderos` (
  `id_producto` int(11) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `descripcion_producto` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `estado_oferta` tinyint(4) DEFAULT 0,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `precio_anterior` decimal(10,2) DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productosganaderos`
--

INSERT INTO `productosganaderos` (`id_producto`, `nombre_producto`, `descripcion_producto`, `categoria_id`, `cantidad`, `estado_oferta`, `fecha_publicacion`, `id_usuario`, `imagen_url`, `precio_anterior`, `precio_unitario`) VALUES
(7, 'Alimento Concentrado para Cerdos', 'Bolsa de 25kg de alimento concentrado para cerdos', 5, 75, 0, '2025-06-02 22:04:54', 1, '/LoginADSO/public/assets/images/productos/8a230990fde9dcc06e0576fcf4d398ed.jpg', NULL, 25.00),
(8, 'Pera', 'PeraPeraPeraPera', 1, 2, 1, '2025-06-02 22:24:00', 1, '/LoginADSO/public/assets/images/productos/04e9b5d6feff0d783fad3a6e3278bb1a.jpg', 3000.00, 2500.00),
(9, 'Mango', 'www', 1, 2, 1, '2025-06-02 23:43:44', 1, '/LoginADSO/public/assets/images/productos/ca06ea4e8bc3c3ed4cdc994f5a5bec3f.png', 3.00, 222.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reproducciones`
--

CREATE TABLE `reproducciones` (
  `id_reproduccion` int(11) NOT NULL,
  `id_animal_hembra` int(11) DEFAULT NULL,
  `id_animal_macho` int(11) DEFAULT NULL,
  `fecha_reproduccion` date DEFAULT NULL,
  `fecha_nacimiento_cria` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reproducciones`
--

INSERT INTO `reproducciones` (`id_reproduccion`, `id_animal_hembra`, `id_animal_macho`, `fecha_reproduccion`, `fecha_nacimiento_cria`) VALUES
(1, 1, 2, '2024-09-10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Empleado', 'Acceso limitado para gestionar ventas y productos'),
(3, 'Usuario', 'Acceso a la gestión general de inventarios, simulador y productos');

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
(1, 5),
(2, 1),
(2, 2),
(3, 4),
(3, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stockalimentos`
--

CREATE TABLE `stockalimentos` (
  `id_stock` int(11) NOT NULL,
  `cantidad_utilizada` decimal(10,2) NOT NULL,
  `fecha` date DEFAULT NULL,
  `id_alimento` int(11) DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stockalimentos`
--

INSERT INTO `stockalimentos` (`id_stock`, `cantidad_utilizada`, `fecha`, `id_alimento`, `id_animal`) VALUES
(1, 10.00, '2025-05-01', 1, 1),
(2, 5.00, '2025-05-02', 2, 2);

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
(1, 'Vaca', 'Bovino hembra, generalmente para leche y carne'),
(2, 'Toro', 'Bovino macho, reproductor'),
(3, 'Burro', 'Equino de carga'),
(4, 'Caballo', 'Equino, usado para monta y trabajo'),
(5, 'Cerdo', 'Suino, para carne'),
(6, 'Cabra', 'Caprino, para leche, carne y lana'),
(7, 'Gallina', 'Ave de corral, para huevos y carne');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id_transaccion` int(11) NOT NULL,
  `comprador_vendedor` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `tipo_transaccion` enum('Compra','Venta','Otro') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`id_transaccion`, `comprador_vendedor`, `fecha`, `id_animal`, `precio`, `tipo_transaccion`) VALUES
(1, 'Comprador Ganadero S.A.', '2025-05-10', 1, 1500.00, 'Venta'),
(2, 'Proveedor de Alimentos', '2025-05-05', NULL, 500.00, 'Compra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamientos`
--

CREATE TABLE `tratamientos` (
  `id_tratamiento` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `tipo_tratamiento` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tratamientos`
--

INSERT INTO `tratamientos` (`id_tratamiento`, `descripcion`, `fecha_inicio`, `fecha_fin`, `id_animal`, `tipo_tratamiento`) VALUES
(1, 'Tratamiento para infección mamaria', '2025-04-29', '2025-05-05', 1, 'Antibiótico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  `correo_usuario` varchar(255) NOT NULL,
  `contrasena_usuario` varchar(255) NOT NULL,
  `direccion_usuario` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo','Otro') DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_ultimo_acceso` timestamp NULL DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `imagen_url_Usuario` VARCHAR(255) DEFAULT NULL,
  `nombreCompleto` varchar(255) DEFAULT NULL,
  `telefono_usuario` VARCHAR(15) DEFAULT NULL,
  `token_expiracion` datetime DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `correo_usuario`, `contrasena_usuario`, `direccion_usuario`, `estado`, `fecha_creacion`, `fecha_ultimo_acceso`, `id_rol`, `nombreCompleto`, `telefono_usuario`, `token_expiracion`, `token_recuperacion`) VALUES
(1, 'JoseD', 'josedominguez.121398@gmail.com', '$2y$10$jqggiIAtb4r0rmIy3hCC1us0PDdkoOdYUqyRiWO9A4ZNXyxb1dvl.', NULL, NULL, '2025-06-03 02:54:18', NULL, 1, 'Jose Dominguez Cuero', NULL, NULL, NULL),
(2, 'admin', 'admin@example.com', 'hashed_password_admin', NULL, 'Activo', '2025-06-03 03:04:54', NULL, 1, 'Admin General', 123456789, NULL, NULL),
(3, 'empleado1', 'empleado1@example.com', 'hashed_password_empleado', NULL, 'Activo', '2025-06-03 03:04:54', NULL, 2, 'Juan Pérez', 987654321, NULL, NULL),
(4, 'usuario', 'usuario@example.com', 'hashed_password_veterinario', NULL, 'Activo', '2025-06-03 03:04:54', NULL, 3, 'Dr. Ana Gómez', 555112233, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total_venta` decimal(10,2) DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_animal`, `cantidad`, `precio_unitario`, `total_venta`, `fecha_venta`) VALUES
(1, 4, 1, 1200.00, 1200.00, '2025-03-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventasproductos`
--

CREATE TABLE `ventasproductos` (
  `id_venta_producto` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `total_venta` decimal(10,2) DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `campanas`
--
ALTER TABLE `campanas`
  ADD PRIMARY KEY (`id_campana`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `categoriasproducto`
--
ALTER TABLE `categoriasproducto`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indices de la tabla `detallespedido`
--
ALTER TABLE `detallespedido`
  ADD PRIMARY KEY (`id_detalle_pedido`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

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
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
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
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  ADD PRIMARY KEY (`id_reproduccion`),
  ADD KEY `id_animal_hembra` (`id_animal_hembra`),
  ADD KEY `id_animal_macho` (`id_animal_macho`);

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
  -- ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo_usuario` (`correo_usuario`);

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
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `animales`
--
ALTER TABLE `animales`
  MODIFY `id_animal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `campanas`
--
ALTER TABLE `campanas`
  MODIFY `id_campana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categoriasproducto`
--
ALTER TABLE `categoriasproducto`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detallespedido`
--
ALTER TABLE `detallespedido`
  MODIFY `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventarioalimentos`
--
ALTER TABLE `inventarioalimentos`
  MODIFY `id_alimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `logsactividades`
--
ALTER TABLE `logsactividades`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `productosganaderos`
--
ALTER TABLE `productosganaderos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  MODIFY `id_reproduccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `stockalimentos`
--
ALTER TABLE `stockalimentos`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tiposanimales`
--
ALTER TABLE `tiposanimales`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id_transaccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tratamientos`
--
ALTER TABLE `tratamientos`
  MODIFY `id_tratamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventasproductos`
--
ALTER TABLE `ventasproductos`
  MODIFY `id_venta_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `animales_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `campanas`
--
ALTER TABLE `campanas`
  ADD CONSTRAINT `campanas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `detallespedido`
--
ALTER TABLE `detallespedido`
  ADD CONSTRAINT `detallespedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `detallespedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productosganaderos` (`id_producto`);

--
-- Filtros para la tabla `logsactividades`
--
ALTER TABLE `logsactividades`
  ADD CONSTRAINT `logsactividades_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productosganaderos`
--
ALTER TABLE `productosganaderos`
  ADD CONSTRAINT `productosganaderos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categoriasproducto` (`id_categoria`),
  ADD CONSTRAINT `productosganaderos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `reproducciones`
--
ALTER TABLE `reproducciones`
  ADD CONSTRAINT `reproducciones_ibfk_1` FOREIGN KEY (`id_animal_hembra`) REFERENCES `animales` (`id_animal`),
  ADD CONSTRAINT `reproducciones_ibfk_2` FOREIGN KEY (`id_animal_macho`) REFERENCES `animales` (`id_animal`);

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
