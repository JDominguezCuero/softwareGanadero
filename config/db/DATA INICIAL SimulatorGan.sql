-- 1. Insertar algunos roles
INSERT INTO Roles (nombre_rol, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Empleado', 'Acceso limitado para gestionar ventas y productos'),
('Veterinario', 'Acceso a la gestión de salud animal');

-- 2. Insertar usuarios de prueba
INSERT INTO Usuarios (nombreCompleto, nombre_usuario, correo_usuario, contrasena_usuario, id_rol, estado) VALUES
('ADMIN', 'admin', 'admin@example.com', 'hashed_password_admin', 1, 'Activo'),
('Empleado', 'empleado1', 'empleado1@example.com', 'hashed_password_empleado', 2, 'Activo'),
('Veterinacio', 'veterinario1', 'veterinario1@example.com', 'hashed_password_veterinario', 3, 'Activo');

-- 3. Insertar permisos básicos
INSERT INTO Permisos (nombre_permiso, descripcion) VALUES
('Gestionar Productos', 'Crear, editar y eliminar productos ganaderos'),
('Gestionar Ventas', 'Registrar y ver ventas de productos'),
('Gestionar Usuarios', 'Crear, editar y eliminar usuarios'),
('Ver Reportes', 'Acceso a reportes de gestión');

-- 4. Asignar permisos a los roles

-- El administrador tiene todos los permisos
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4);

-- El empleado solo puede gestionar ventas y productos
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(2, 1),
(2, 2);

-- El veterinario puede ver reportes
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(3, 4);


-- 5. Insertar productos ganaderos de prueba
INSERT INTO ProductosGanaderos (nombre_producto, descripcion_producto, precio_unitario) VALUES
('Leche de vaca', 'Litro de leche fresca de ganado', 2.50),
('Carne de res', 'Kilogramo de carne de alta calidad', 8.00),
('Queso artesanal', 'Queso producido de manera tradicional', 5.00),
('Yogur natural', 'Yogur elaborado con leche fresca', 3.00);

-- 6. Insertar ventas de productos de prueba
INSERT INTO VentasProductos (id_producto, cantidad, precio_unitario, fecha_venta) VALUES
(1, 10, 2.50, '2025-04-28'), -- Venta de leche
(2, 5, 8.00, '2025-04-27'),  -- Venta de carne
(3, 7, 5.00, '2025-04-26'),  -- Venta de queso
(4, 3, 3.00, '2025-04-25');  -- Venta de yogur

-- 7. Insertar logs de actividad de ejemplo
INSERT INTO LogsActividades (id_usuario, actividad) VALUES
(1, 'Accedió al sistema'),
(2, 'Registró una venta de productos'),
(3, 'Consultó reportes de animales');


