-- Garapen ingurunerako datu-basearen egitura eta hasierako datuak.
-- OHARRA: Script hau exekutatzean, lehendik zeuden taulak ezabatuko dira.

-- Kodifikazioa eta konparazioa ezarri
SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

--
-- `langileak` taularen egitura (kontuak integratuta)
--
CREATE TABLE `langileak` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `izena` VARCHAR(255) NOT NULL,
  `abizena` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `departamentua` ENUM('Gerentzia', 'Administrazioa', 'IKT', 'Enologia', 'Mahats-biltzaileak') NOT NULL,
  `kontratazio_data` DATE DEFAULT NULL,
  `erabiltzailea` VARCHAR(255) NOT NULL UNIQUE, -- Kontuaren erabiltzailea
  `pasahitza` VARCHAR(255) NOT NULL,           -- Kontuaren pasahitza (hashetuta)
  `is_admin` BOOLEAN NOT NULL DEFAULT FALSE,   -- Administratzailea den ala ez
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- `oharrak` taularen egitura
--
CREATE TABLE `oharrak` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulua` VARCHAR(255) NOT NULL,
  `edukia` TEXT NOT NULL,
  `data` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Hasierako datuak sartu
--

-- Administrari langilea sortu (erabiltzailea: admin, pasahitza: admin)
-- Pasahitza BCrypt erabiliz hashetuta dago
INSERT INTO `langileak` (`izena`, `abizena`, `email`, `departamentua`, `kontratazio_data`, `erabiltzailea`, `pasahitza`, `is_admin`) VALUES
('Admin', 'User', 'admin@unanue.eus', 'IKT', CURDATE(), 'admin', '$2y$10$yoC/DM9bePpk86Jp5HKikObqOUKqQVsiDPh9IqgHcca0EXEZpAZbS', 1);


SET foreign_key_checks = 1;
