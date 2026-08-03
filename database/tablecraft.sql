CREATE DATABASE IF NOT EXISTS tablecraft_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tablecraft_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS custom_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  columns_json JSON NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX(user_id),
  CONSTRAINT fk_custom_categories_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS saved_tables (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  category VARCHAR(80) NOT NULL,
  columns_json JSON NOT NULL,
  rows_json JSON NOT NULL,
  notes TEXT NULL,
  theme_mode VARCHAR(20) NOT NULL DEFAULT 'bright',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX(user_id),
  INDEX(category),
  CONSTRAINT fk_saved_tables_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, created_at)
VALUES ('Demo User', 'demo@tablecraft.local', '$2y$12$Y8dnKMkHAaaDCeLI7To3Oe1a.fDJAjcvXQ1x9Dq.iLkUIBgTw5Tyy', NOW())
ON DUPLICATE KEY UPDATE email=email;
