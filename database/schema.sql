CREATE DATABASE IF NOT EXISTS db_sistema_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sistema_web;
CREATE TABLE IF NOT EXISTS estudiantes (
 id INT NOT NULL AUTO_INCREMENT,
 nombres VARCHAR(100) NOT NULL,
 apellidos VARCHAR(100) NOT NULL,
 cedula VARCHAR(10) NOT NULL,
 correo VARCHAR(150) NOT NULL,
 carrera VARCHAR(150) NOT NULL,
 semestre INT NOT NULL,
 fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id),
 CONSTRAINT uq_estudiantes_cedula UNIQUE (cedula),
 CONSTRAINT uq_estudiantes_correo UNIQUE (correo),
 CONSTRAINT ck_estudiantes_semestre CHECK (semestre BETWEEN 1 AND 12),
 CONSTRAINT ck_estudiantes_cedula CHECK (cedula REGEXP '^[0-9]{10}$')
) ENGINE=InnoDB;
