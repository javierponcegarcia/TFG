-- Eliminar la base de datos si existe para evitar errores de duplicación

DROP DATABASE IF EXISTS tienda_zapatillas;

-- Crear la base de datos tienda_zapatillas

CREATE DATABASE tienda_zapatillas;

-- Seleccionar la base de datos tienda_zapatillas para las siguientes operaciones

USE tienda_zapatillas;

-- Crear la tabla categorias

CREATE TABLE categorias (

    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL

);

-- Crear la tabla productos

CREATE TABLE productos (

    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    categoria_id INT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)

);



-- Crear la tabla inventario
CREATE TABLE inventario (

    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    cantidad INT NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)

);

-- Insertar registros en la tabla categorias

INSERT INTO categorias (nombre) VALUES ('Deportiva'), ('Casual'), ('Formal');

