CREATE DATABASE IF NOT EXISTS arepas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arepas;
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  clave_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin','empleado') NOT NULL DEFAULT 'empleado',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(40) NOT NULL UNIQUE,
  orden TINYINT NOT NULL DEFAULT 0
) ENGINE=InnoDB;
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NOT NULL,
  nombre VARCHAR(80) NOT NULL,
  descripcion VARCHAR(200) NOT NULL DEFAULT '',
  precio INT UNSIGNED NOT NULL,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  creado_por INT NULL,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO categorias (nombre, orden) VALUES ('Arepas',1),('Perros calientes',2),('Chuzos',3),('Bebidas',4);
INSERT INTO productos (categoria_id,nombre,descripcion,precio) VALUES
(1,'Arepa de huevo','Frita, crocante por fuera y con huevo adentro',3500),
(1,'Arepa con queso','Asada, rellena de queso costeño derretido',4000),
(1,'Arepa mixta','Carne desmechada, pollo y queso',7000),
(2,'Perro sencillo','Salchicha, papitas, salsas de la casa',6000),
(2,'Perro especial','Salchicha, tocineta, queso gratinado y huevo de codorniz',10000),
(3,'Chuzo de pollo','Con papa criolla y arepita',9000),
(3,'Chuzo de carne','Res marinada al carbón, con yuca',11000),
(3,'Chuzo desgranado','Carne, pollo, chorizo, maíz y queso',16000),
(4,'Jugo natural','Mango, lulo o maracuyá',3500);
