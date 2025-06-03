DROP DATABASE IF EXISTS ganaderiasimulador;
CREATE DATABASE ganaderiasimulador;
USE ganaderiasimulador;

-- 1. tiposanimales
CREATE TABLE tiposanimales (
    id_tipo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB;

-- 2. usuarios
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(255) UNIQUE NOT NULL,
    correo_usuario VARCHAR(255) UNIQUE NOT NULL,
    contrasena_usuario VARCHAR(255) NOT NULL,
    direccion_usuario VARCHAR(255) NULL,
    estado ENUM('Activo', 'Inactivo', 'Otro') NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_acceso TIMESTAMP NULL,
    id_rol INT NULL,
    nombreCompleto VARCHAR(255) NULL,
    telefono_usuario INT NULL,
    token_expiracion DATETIME NULL,
    token_recuperacion VARCHAR(255) NULL
) ENGINE=InnoDB;

-- 3. roles
CREATE TABLE roles (
    id_rol INT PRIMARY KEY AUTO_INCREMENT,
    nombre_rol VARCHAR(100) NOT NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB;

-- 4. permisos
CREATE TABLE permisos (
    id_permiso INT PRIMARY KEY AUTO_INCREMENT,
    nombre_permiso VARCHAR(100) NOT NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB;

-- 5. rolespermisos
CREATE TABLE rolespermisos (
    id_rol INT NOT NULL,
    id_permiso INT NOT NULL,
    PRIMARY KEY (id_rol, id_permiso),
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso)
) ENGINE=InnoDB;

-- 6. categoriasproducto
CREATE TABLE categoriasproducto (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(255) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- 7. productosganaderos
CREATE TABLE productosganaderos (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(255) NOT NULL,
    descripcion_producto TEXT NULL,
    categoria_id INT NULL,
    cantidad INT NOT NULL, -- Columna 'cantidad' añadida
    estado_oferta TINYINT DEFAULT 0,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    imagen_url VARCHAR(255) NULL,
    precio_anterior DECIMAL(10,2) NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categoriasproducto(id_categoria),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- 8. pedidos
CREATE TABLE pedidos (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    direccion_envio TEXT NOT NULL,
    estado_pedido VARCHAR(50) DEFAULT 'Pendiente',
    fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    total_pedido DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- 9. detallespedido
CREATE TABLE detallespedido (
    id_detalle_pedido INT PRIMARY KEY AUTO_INCREMENT,
    cantidad INT NOT NULL,
    id_pedido INT NULL,
    id_producto INT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productosganaderos(id_producto)
) ENGINE=InnoDB;

-- 10. animales
CREATE TABLE animales (
    id_animal INT PRIMARY KEY AUTO_INCREMENT,
    id_tipo INT NULL,
    id_usuario INT NULL,
    nombre VARCHAR(255) NOT NULL,
    edad INT NULL,
    peso DECIMAL(10,2) NULL,
    produccion INT NULL,
    alimentacion INT NULL,
    higiene INT NULL,
    salud INT NULL,
    estado ENUM('Activo', 'Inactivo', 'Otro') NULL,
    fecha_ingreso DATE NULL,
    last_updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tipo) REFERENCES tiposanimales(id_tipo),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- 11. actividades
CREATE TABLE actividades (
    id_actividad INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT NULL,
    fecha DATE NULL,
    id_animal INT NULL,
    tipo_actividad ENUM('Tipo1','Tipo2','Tipo3') NULL,
    FOREIGN KEY (id_animal) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 12. campanas
CREATE TABLE campanas (
    id_campana INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT NOT NULL,
    estado ENUM('Activo', 'Inactivo') NULL,
    fecha_evento DATETIME NOT NULL,
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    imagen_url VARCHAR(255) NULL,
    titulo VARCHAR(255) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- 13. inventarioalimentos
CREATE TABLE inventarioalimentos (
    id_alimento INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    unidad_medida ENUM('Kg') NULL,
    fecha_ingreso DATE NULL
) ENGINE=InnoDB;

-- 14. logsactividades
CREATE TABLE logsactividades (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    actividad TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- 15. reproducciones
CREATE TABLE reproducciones (
    id_reproduccion INT PRIMARY KEY AUTO_INCREMENT,
    id_animal_hembra INT NULL,
    id_animal_macho INT NULL,
    fecha_reproduccion DATE NULL,
    fecha_nacimiento_cria DATE NULL,
    FOREIGN KEY (id_animal_hembra) REFERENCES animales(id_animal),
    FOREIGN KEY (id_animal_macho) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 16. stockalimentos
CREATE TABLE stockalimentos (
    id_stock INT PRIMARY KEY AUTO_INCREMENT,
    cantidad_utilizada DECIMAL(10,2) NOT NULL,
    fecha DATE NULL,
    id_alimento INT NULL,
    id_animal INT NULL,
    FOREIGN KEY (id_alimento) REFERENCES inventarioalimentos(id_alimento),
    FOREIGN KEY (id_animal) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 17. transacciones
CREATE TABLE transacciones (
    id_transaccion INT PRIMARY KEY AUTO_INCREMENT,
    comprador_vendedor VARCHAR(255) NULL,
    fecha DATE NULL,
    id_animal INT NULL,
    precio DECIMAL(10,2) NULL,
    tipo_transaccion ENUM('Compra', 'Venta', 'Otro') NULL,
    FOREIGN KEY (id_animal) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 18. tratamientos
CREATE TABLE tratamientos (
    id_tratamiento INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    id_animal INT NULL,
    tipo_tratamiento VARCHAR(255) NULL,
    FOREIGN KEY (id_animal) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 19. ventas
CREATE TABLE ventas (
    id_venta INT PRIMARY KEY AUTO_INCREMENT,
    id_animal INT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total_venta DECIMAL(10,2) NULL,
    fecha_venta DATE NULL,
    FOREIGN KEY (id_animal) REFERENCES animales(id_animal)
) ENGINE=InnoDB;

-- 20. ventasproductos
CREATE TABLE ventasproductos (
    id_venta_producto INT PRIMARY KEY AUTO_INCREMENT,
    id_producto INT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total_venta DECIMAL(10,2) NULL,
    fecha_venta DATE NULL,
    FOREIGN KEY (id_producto) REFERENCES productosganaderos(id_producto)
) ENGINE=InnoDB;