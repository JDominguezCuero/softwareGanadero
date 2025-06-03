
INSERT INTO Roles (nombre_rol, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Empleado', 'Acceso limitado para gestionar ventas y productos'),
('Veterinario', 'Acceso a la gestión de salud animal');

-- 2. Insertar tipos de animales
INSERT INTO TiposAnimales (nombre, descripcion) VALUES
('Vaca', 'Bovino hembra, generalmente para leche y carne'),
('Toro', 'Bovino macho, reproductor'),
('Burro', 'Equino de carga'),
('Caballo', 'Equino, usado para monta y trabajo'),
('Cerdo', 'Suino, para carne'),
('Cabra', 'Caprino, para leche, carne y lana'),
('Gallina', 'Ave de corral, para huevos y carne');

-- 3. Insertar categorías de productos
INSERT INTO CategoriasProducto (nombre_categoria) VALUES
('Ofertas'),
('Productos Frescos'),
('Lácteos y Huevos'),
('Carnes y Embutidos'),
('Alimentos para Animales'),
('Equipos y Suministros');

-- 4. Insertar usuarios de prueba
INSERT INTO Usuarios (nombreCompleto, nombre_usuario, correo_usuario, contrasena_usuario, id_rol, estado, telefono_usuario) VALUES
('Admin General', 'admin', 'admin@example.com', 'hashed_password_admin', 1, 'Activo', 123456789)

-- 5. Insertar permisos básicos
INSERT INTO Permisos (nombre_permiso, descripcion) VALUES
('Gestionar Productos', 'Crear, editar y eliminar productos ganaderos'),
('Gestionar Ventas', 'Registrar y ver ventas de productos'),
('Gestionar Usuarios', 'Crear, editar y eliminar usuarios'),
('Ver Reportes', 'Acceso a reportes de gestión'),
('Gestionar Animales', 'Registrar y actualizar información de animales');

-- 6. Asignar permisos a los roles

-- El administrador tiene todos los permisos
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(1, 1), -- Gestionar Productos
(1, 2), -- Gestionar Ventas
(1, 3), -- Gestionar Usuarios
(1, 4), -- Ver Reportes
(1, 5); -- Gestionar Animales

-- El empleado solo puede gestionar ventas y productos
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(2, 1), -- Gestionar Productos
(2, 2); -- Gestionar Ventas

-- El veterinario puede ver reportes y gestionar animales
INSERT INTO RolesPermisos (id_rol, id_permiso) VALUES
(3, 4), -- Ver Reportes
(3, 5); -- Gestionar Animales

-- 9. Insertar logs de actividad de ejemplo
INSERT INTO LogsActividades (id_usuario, actividad) VALUES
(1, 'Accedió al sistema y actualizó precios de productos'),
(2, 'Registró una venta de productos al cliente X'),
(3, 'Consultó reportes de salud animal para el ganado');

-- 11. Insertar inventario de alimentos
INSERT INTO InventarioAlimentos (nombre, cantidad, unidad_medida, fecha_ingreso) VALUES
('Pasto Seco', 1000.00, 'Kg', '2025-01-01'),
('Maíz Molido', 500.00, 'Kg', '2025-02-15'),
('Suplemento Mineral', 50.00, 'Kg', '2025-03-10');

-- 12. Insertar stock de alimentos (ejemplo de consumo)
INSERT INTO StockAlimentos (id_alimento, id_animal, cantidad_utilizada, fecha) VALUES
(1, 1, 10.00, '2025-05-01'), -- Lola comió 10kg de Pasto Seco
(2, 2, 5.00, '2025-05-02'); -- Toro Fuerte comió 5kg de Maíz Molido

-- 13. Insertar actividades de animales
INSERT INTO Actividades (id_animal, tipo_actividad, descripcion, fecha) VALUES
(1, 'Tipo1', 'Chequeo de rutina y ordeño', '2025-05-01'),
(2, 'Tipo2', 'Vacunación anual', '2025-04-20');

-- 14. Insertar reproducciones (ejemplo)
INSERT INTO Reproducciones (id_animal_hembra, id_animal_macho, fecha_reproduccion, fecha_nacimiento_cria) VALUES
(1, 2, '2024-09-10', NULL); -- Lola y Toro Fuerte, fecha de reproducción

-- 15. Insertar tratamientos (ejemplo)
INSERT INTO Tratamientos (id_animal, tipo_tratamiento, descripcion, fecha_inicio, fecha_fin) VALUES
(1, 'Antibiótico', 'Tratamiento para infección mamaria', '2025-04-29', '2025-05-05');

-- 16. Insertar ventas de animales (ejemplo)
INSERT INTO Ventas (id_animal, cantidad, precio_unitario, total_venta, fecha_venta) VALUES
(4, 1, 1200.00, 1200.00, '2025-03-15'); -- Venta de un burro

-- 17. Insertar transacciones (ejemplo)
INSERT INTO Transacciones (id_animal, tipo_transaccion, precio, comprador_vendedor, fecha) VALUES
(1, 'Venta', 1500.00, 'Comprador Ganadero S.A.', '2025-05-10'),
(NULL, 'Compra', 500.00, 'Proveedor de Alimentos', '2025-05-05'); -- Ejemplo de compra de alimento no asociado a un animal específico

-- 18. Insertar campañas (ejemplo)
INSERT INTO Campanas (titulo, descripcion, fecha_evento, ubicacion, id_usuario, estado, imagen_url) VALUES
('Feria Ganadera 2025', 'Gran exposición de ganado y productos agrícolas', '2025-07-20 10:00:00', 'Recinto Ferial de Boyacá', 1, 'Activo', '/LoginADSO/public/assets/images/placeholder_campana.png');