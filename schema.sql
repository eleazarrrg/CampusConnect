CREATE DATABASE IF NOT EXISTS campus_connect CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE campus_connect;
DROP TABLE IF EXISTS login_audit;
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS appointment_history;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS loans_history;
DROP TABLE IF EXISTS loans;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS search_stats;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  id_card VARCHAR(32) NOT NULL UNIQUE,
  email VARCHAR(160) NOT NULL UNIQUE,
  phone VARCHAR(32) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('pending','active','blocked') NOT NULL DEFAULT 'pending',
  verify_token VARCHAR(64),
  tz VARCHAR(64) DEFAULT 'America/Panama',
  attempts INT NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE login_audit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  email VARCHAR(160) NULL,
  ip VARCHAR(64) NOT NULL,
  success TINYINT(1) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(80) NOT NULL,
  ip VARCHAR(64) NOT NULL,
  meta JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  status ENUM('new','seen','failed','resent') NOT NULL DEFAULT 'new',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  seen_at DATETIME NULL
);

CREATE TABLE appointment_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  appointment_id INT NOT NULL,
  user_id INT NOT NULL,
  old_date DATE NULL,
  old_time TIME NULL,
  new_date DATE NULL,
  new_time TIME NULL,
  action ENUM('create','modify','cancel') NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  status ENUM('reservada','cancelada') NOT NULL DEFAULT 'reservada',
  duration_min INT NOT NULL DEFAULT 30,
  responsible VARCHAR(120) DEFAULT 'Orientador B.E.',
  location VARCHAR(120) DEFAULT 'Campus UTP',
  modality ENUM('Presencial','Virtual') DEFAULT 'Presencial',
  created_utc DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slot (date, time),
  INDEX idx_user_active (user_id, status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE loans_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  loan_id INT NOT NULL,
  user_id INT NOT NULL,
  action ENUM('create','update','finalize','delete') NOT NULL,
  meta JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE loans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  days INT NOT NULL,
  status ENUM('proceso','activo','finalizado','cancelado') NOT NULL DEFAULT 'activo',
  request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  pickup_date DATE NULL,
  due_date DATE NULL,
  return_date DATE NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(160) NULL,
  published_date DATE NULL,
  description TEXT NULL,
  cover_url VARCHAR(255) NULL,
  status ENUM('Disponible','No disponible') NOT NULL DEFAULT 'Disponible'
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  user_id INT NULL,
  text TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  image_url VARCHAR(255) NULL,
  pub_date DATE NULL,
  location VARCHAR(120) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  likes INT NOT NULL DEFAULT 0
);

CREATE TABLE search_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  q VARCHAR(120) NULL,
  category VARCHAR(60) NULL,
  date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  category VARCHAR(60) NOT NULL,
  date DATE NOT NULL,
  time TIME NULL,
  place VARCHAR(120) NULL,
  seats INT NULL,
  requirements TEXT NULL
);

INSERT INTO books (title, author, published_date, description, cover_url, status) VALUES
('Clean Code','Robert C. Martin','2008-08-01','Buenas practicas','https://picsum.photos/seed/cc/300/180','Disponible'),
('Design Patterns','GoF','1994-10-31','Patrones clasicos','https://picsum.photos/seed/dp/300/180','Disponible'),
('Refactoring','Martin Fowler','1999-07-08','Tecnicas de refactorizacion','https://picsum.photos/seed/rf/300/180','No disponible'),
('Domain-Driven Design','Eric Evans','2003-08-30','Modelo de dominio','https://picsum.photos/seed/ddd/300/180','Disponible'),
('JavaScript: The Good Parts','Douglas Crockford','2008-05-01','JS clasico','https://picsum.photos/seed/jsgp/300/180','Disponible');

INSERT INTO posts (title, body, image_url, pub_date, location, is_active) VALUES
('Bienvenidos','Primera publicacion de la plataforma.','https://picsum.photos/seed/cc1/600/300','2099-02-01','Campus UTP - Biblioteca',1),
('Lanzamiento','Detalles del lanzamiento.','https://picsum.photos/seed/cc2/600/300','2099-02-05','Edificio A',1);

INSERT INTO events (title, category, date, time, place, seats, requirements) VALUES
('Taller de Node','Talleres','2099-02-01','10:00:00','Lab 101',30,'Registro previo'),
('Seminario UX','Seminarios','2099-02-05','14:00:00','Aula Magna',120,'Entrar 10 min antes'),
('Feria Libros','Feria','2099-03-10','09:00:00','Plaza Central',200,'Libre');
