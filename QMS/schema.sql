--1) Create database 
CREATE DATABASE IF NOT EXISTS qms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qms;

-- 2) Users
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id VARCHAR(32) NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('student','admin') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_student_id (student_id),
  UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Services
CREATE TABLE IF NOT EXISTS services (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_service_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Queues (office/service lines like "Finance", "Library")
CREATE TABLE IF NOT EXISTS queues (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  service_id INT UNSIGNED NULL,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NULL,
  average_service_seconds INT UNSIGNED NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_queue_name (name),
  KEY idx_queue_service (service_id),
  CONSTRAINT fk_queue_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Queue Entries (each student's place in a queue)
-- Ordering is by (queue_id, id). Position is computed as count of entries with same queue_id, status='waiting' and id < current.
CREATE TABLE IF NOT EXISTS queue_entries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  queue_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  status ENUM('waiting','served','no_show','left') NOT NULL DEFAULT 'waiting',
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  served_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_queue_status (queue_id, status, id),
  KEY idx_user_queue (user_id, queue_id),
  CONSTRAINT fk_qe_queue FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE,
  CONSTRAINT fk_qe_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
