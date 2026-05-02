CREATE DATABASE IF NOT EXISTS `volleycup4.0`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `volleycup4.0`;

CREATE TABLE IF NOT EXISTS `registrations` (
  `id` VARCHAR(32) NOT NULL,
  `university_name` VARCHAR(255) NOT NULL,
  `captain` VARCHAR(255) NOT NULL,
  `team_name` VARCHAR(255) NOT NULL,
  `team_name` VARCHAR(255) NOT NULL,
  `roster_size` TINYINT UNSIGNED NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `category` VARCHAR(20) NOT NULL,
  `services_json` TEXT NOT NULL,
  `comments` TEXT NOT NULL,
  `team_photo` VARCHAR(255) DEFAULT NULL,
  `team_photo` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'confirmed',
  `submitted_at` DATETIME NOT NULL,
  `cancelled_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
