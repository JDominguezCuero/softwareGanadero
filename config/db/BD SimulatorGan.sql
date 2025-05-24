-- Creación de la base de datos
CREATE DATABASE GanaderiaSimulador;

-- Seleccionar la base de datos a usar
USE GanaderiaSimulador;

-- Tabla para los usuarios
CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombreCompleto VARCHAR(255),
    nombre_usuario VARCHAR(100) NOT NULL UNIQUE,
    correo_usuario VARCHAR(100) NOT NULL UNIQUE,
    contrasena_usuario VARCHAR(255) NOT NULL, -- Contraseña en formato hash
    id_rol INT NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP NULL, -- Solo declaramos que puede ser NULL
    FOREIGN KEY (id_rol) REFERENCES Roles(id_rol)
);

-- Tabla para los roles
CREATE TABLE Roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion TEXT
);

-- Tabla para los permisos
CREATE TABLE Permisos (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_permiso VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- Tabla para la relación entre roles y permisos
CREATE TABLE RolesPermisos (
    id_rol INT NOT NULL,
    id_permiso INT NOT NULL,
    PRIMARY KEY (id_rol, id_permiso),
    FOREIGN KEY (id_rol) REFERENCES Roles(id_rol),
    FOREIGN KEY (id_permiso) REFERENCES Permisos(id_permiso)
);

-- Tabla para los logs de actividad de los usuarios (para auditoría y seguridad)
CREATE TABLE LogsActividades (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    actividad TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Tabla para los tipos de animales
CREATE TABLE TiposAnimales (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- Tabla para los animales
CREATE TABLE Animales (
    id_animal INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_tipo INT NOT NULL,
    fecha_nacimiento DATE,
    peso DECIMAL(10, 2),
    estado ENUM('Activo', 'Enfermo', 'Muerto') DEFAULT 'Activo',
    fecha_ingreso DATE,
    FOREIGN KEY (id_tipo) REFERENCES TiposAnimales(id_tipo)
);

-- Tabla para el inventario de alimentos
CREATE TABLE InventarioAlimentos (
    id_alimento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    unidad_medida ENUM('Kg', 'L') DEFAULT 'Kg',
    fecha_ingreso DATE
);

-- Tabla para las actividades (ej. alimentación, vacunación, etc.)
CREATE TABLE Actividades (
    id_actividad INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    descripcion TEXT,
    fecha DATE,
    tipo_actividad ENUM('Alimentación', 'Vacunación', 'Manejo', 'Reproducción') NOT NULL,
    FOREIGN KEY (id_animal) REFERENCES Animales(id_animal)
);

-- Tabla para el control de medicamentos o tratamientos
CREATE TABLE Tratamientos (
    id_tratamiento INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    tipo_tratamiento VARCHAR(100),
    FOREIGN KEY (id_animal) REFERENCES Animales(id_animal)
);

-- Tabla para registrar las transacciones de compra y venta de animales
CREATE TABLE Transacciones (
    id_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    tipo_transaccion ENUM('Compra', 'Venta') NOT NULL,
    fecha DATE,
    precio DECIMAL(10, 2),
    comprador_vendedor VARCHAR(100),
    FOREIGN KEY (id_animal) REFERENCES Animales(id_animal)
);

-- Tabla para los registros de stock de alimentos utilizados
CREATE TABLE StockAlimentos (
    id_stock INT AUTO_INCREMENT PRIMARY KEY,
    id_alimento INT NOT NULL,
    cantidad_utilizada DECIMAL(10, 2) NOT NULL,
    id_animal INT NOT NULL,
    fecha DATE,
    FOREIGN KEY (id_alimento) REFERENCES InventarioAlimentos(id_alimento),
    FOREIGN KEY (id_animal) REFERENCES Animales(id_animal)
);

-- Tabla para registrar las ventas realizadas (en el contexto de la gestión de inventarios)
CREATE TABLE Ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    fecha_venta DATE,
    total_venta DECIMAL(10, 2) AS (cantidad * precio_unitario) STORED,
    FOREIGN KEY (id_animal) REFERENCES Animales(id_animal)
);

-- Tabla para registrar los eventos de reproducción de los animales
CREATE TABLE Reproducciones (
    id_reproduccion INT AUTO_INCREMENT PRIMARY KEY,
    id_animal_macho INT NOT NULL,
    id_animal_hembra INT NOT NULL,
    fecha_reproduccion DATE,
    fecha_nacimiento_cria DATE,
    FOREIGN KEY (id_animal_macho) REFERENCES Animales(id_animal),
    FOREIGN KEY (id_animal_hembra) REFERENCES Animales(id_animal)
);

-- Tabla para las ventas de productos relacionados con la ganadería (leche, lana, etc.)
CREATE TABLE ProductosGanaderos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10, 2),
    descripcion_producto VARCHAR(255),
    unidad_medida ENUM('Litros', 'Kg') NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    fecha_venta DATE
);

-- Tabla para registrar la venta de productos ganaderos
CREATE TABLE VentasProductos (
    id_venta_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL, -- Guardamos el precio aquí
    total_venta DECIMAL(10, 2) AS (cantidad * precio_unitario) STORED, -- Ahora sí permitido
    fecha_venta DATE,
    FOREIGN KEY (id_producto) REFERENCES ProductosGanaderos(id_producto)
);