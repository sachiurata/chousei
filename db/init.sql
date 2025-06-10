CREATE DATABASE IF NOT EXISTS chousei;
USE chousei;

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` char(100) NOT NULL,
  `event_description` text DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `dates` (
  `date_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`date_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `dates_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `participants` (
  `participant_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `participant_name` char(20) NOT NULL,
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`participant_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `attendances` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `attendance` enum('0','1','2','') NOT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `date_id` (`date_id`),
  KEY `participant_id` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
