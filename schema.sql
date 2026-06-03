CREATE DATABASE IF NOT EXISTS mydevpapdatabase
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mydevpapdatabase;

CREATE TABLE IF NOT EXISTS users (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username          VARCHAR(100)  NOT NULL,
    email             VARCHAR(255)  NOT NULL UNIQUE,
    password          VARCHAR(255)  NOT NULL DEFAULT '',
    is_admin          TINYINT(1)    NOT NULL DEFAULT 0,
    is_verified       TINYINT(1)    NOT NULL DEFAULT 0,
    verified_at       DATETIME               DEFAULT NULL,
    termos_aceites    TINYINT(1)    NOT NULL DEFAULT 0,
    termos_aceites_at DATETIME               DEFAULT NULL,
    tem_conversa      TINYINT(1)    NOT NULL DEFAULT 0,
    created_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at        DATETIME               DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS email_verifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token_hash VARCHAR(64)  NOT NULL UNIQUE,
    expires_at DATETIME     NOT NULL,
    used_at    DATETIME              DEFAULT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS conversas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED  NOT NULL,
    titulo     VARCHAR(255)  NOT NULL DEFAULT 'Nova Conversa',
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME               DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mensagens (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversa_id INT UNSIGNED NOT NULL,
    role        ENUM('user','assistant') NOT NULL,
    conteudo    TEXT         NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversa_id) REFERENCES conversas(id) ON DELETE CASCADE
) ENGINE=InnoDB;