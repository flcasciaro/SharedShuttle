-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 29, 2018 alle 11:31
-- Versione del server: 10.1.33-MariaDB
-- Versione PHP: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sharedshuttledb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `credentials`
--

DROP TABLE IF EXISTS `credentials`;
CREATE TABLE `credentials` (
  `Username` varchar(30) NOT NULL,
  `Password` varchar(32) NOT NULL COMMENT '32-bit md5 hash result'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `credentials`
--

INSERT INTO `credentials` (`Username`, `Password`) VALUES
('u1@p.it', 'ec6ef230f1828039ee794566b9c58adc'),
('u2@p.it', '1d665b9b1467944c128a5575119d1cfd'),
('u3@p.it', '7bc3ca68769437ce986455407dab2a1f'),
('u4@p.it', '13207e3d5722030f6c97d69b4904d39d');

-- --------------------------------------------------------

--
-- Struttura della tabella `path`
--

DROP TABLE IF EXISTS `path`;
CREATE TABLE `path` (
  `Stop` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `path`
--

INSERT INTO `path` (`Stop`) VALUES
('AL'),
('BB'),
('DD'),
('EE'),
('FF'),
('KK');

-- --------------------------------------------------------

--
-- Struttura della tabella `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `Username` varchar(30) NOT NULL,
  `Source` varchar(30) NOT NULL,
  `Destination` varchar(30) NOT NULL,
  `Passengers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `reservations`
--

INSERT INTO `reservations` (`Username`, `Source`, `Destination`, `Passengers`) VALUES
('u1@p.it', 'FF', 'KK', 4),
('u2@p.it', 'BB', 'EE', 1),
('u3@p.it', 'DD', 'EE', 1),
('u4@p.it', 'AL', 'DD', 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`Username`);

--
-- Indici per le tabelle `path`
--
ALTER TABLE `path`
  ADD PRIMARY KEY (`Stop`);

--
-- Indici per le tabelle `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`Username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
