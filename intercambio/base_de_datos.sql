-- ══════════════════════════════════════════════════
--  SkillSwap · Script de base de datos
--  Ejecutar en phpMyAdmin o DBeaver
--  Primero crear la base de datos y luego este script
-- ══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS intercambio_habilidades
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE intercambio_habilidades;

-- ── Tabla de usuarios ──────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    carrera    VARCHAR(150)  DEFAULT NULL,
    bio        TEXT          DEFAULT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- ── Tabla de habilidades (catálogo) ───────────────
CREATE TABLE IF NOT EXISTS habilidades (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- ── Relación usuario ↔ habilidades ────────────────
CREATE TABLE IF NOT EXISTS usuario_habilidades (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id   INT  NOT NULL,
    habilidad_id INT  NOT NULL,
    tipo         ENUM('ofrece', 'busca') NOT NULL,
    FOREIGN KEY (usuario_id)   REFERENCES usuarios(id)    ON DELETE CASCADE,
    FOREIGN KEY (habilidad_id) REFERENCES habilidades(id) ON DELETE CASCADE,
    UNIQUE KEY unico_usuario_habilidad_tipo (usuario_id, habilidad_id, tipo)
);

-- ── Solicitudes de intercambio ─────────────────────
CREATE TABLE IF NOT EXISTS solicitudes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    de_usuario_id   INT  NOT NULL,
    para_usuario_id INT  NOT NULL,
    mensaje         TEXT NOT NULL,
    estado          ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (de_usuario_id)   REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (para_usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- ══════════════════════════════════════════════════
--  Habilidades iniciales (podés agregar más)
-- ══════════════════════════════════════════════════
INSERT INTO habilidades (nombre) VALUES
  ('Álgebra'),
  ('Análisis Matemático / Cálculo'),
  ('Física'),
  ('Química'),
  ('Programación en C'),
  ('Programación en Python'),
  ('Programación en Java'),
  ('Desarrollo Web (HTML/CSS)'),
  ('Bases de Datos / SQL'),
  ('Inglés'),
  ('Portugués'),
  ('Francés'),
  ('Estadística'),
  ('Contabilidad'),
  ('Economía'),
  ('Guitarra'),
  ('Piano / Teclado'),
  ('Dibujo técnico'),
  ('Diseño gráfico'),
  ('Edición de video'),
  ('Fotografía'),
  ('Redes y sistemas operativos'),
  ('Escritura y redacción'),
  ('Oratoria y presentaciones');

-- ══════════════════════════════════════════════════
--  Usuarios de prueba (contraseña: "test1234")
-- ══════════════════════════════════════════════════
INSERT INTO usuarios (nombre, email, password, carrera, bio) VALUES
(
  'Valentina López',
  'valen@test.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- "password"
  'Ingeniería en Sistemas, 3er año',
  'Me gusta el álgebra y puedo ayudar con C. Busco aprender guitarra.'
),
(
  'Mateo García',
  'mateo@test.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'Ciencias Económicas, 2do año',
  'Estudio economía pero toco guitarra hace 5 años. Necesito ayuda con programación.'
),
(
  'Lucía Fernández',
  'lucia@test.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'Traductorado de Inglés',
  'Inglés avanzado y algo de francés. Me interesa aprender diseño gráfico.'
);

-- Habilidades de los usuarios de prueba
-- Valentina: ofrece Álgebra y C, busca Guitarra
INSERT INTO usuario_habilidades (usuario_id, habilidad_id, tipo) VALUES
  (1, 1,  'ofrece'),
  (1, 5,  'ofrece'),
  (1, 16, 'busca');

-- Mateo: ofrece Guitarra y Contabilidad, busca Programación en C
INSERT INTO usuario_habilidades (usuario_id, habilidad_id, tipo) VALUES
  (2, 16, 'ofrece'),
  (2, 14, 'ofrece'),
  (2, 5,  'busca');

-- Lucía: ofrece Inglés y Francés, busca Diseño gráfico
INSERT INTO usuario_habilidades (usuario_id, habilidad_id, tipo) VALUES
  (3, 10, 'ofrece'),
  (3, 12, 'ofrece'),
  (3, 19, 'busca');
