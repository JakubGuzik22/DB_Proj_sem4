-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Lip 12, 2025 at 12:12 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `firmakurierska`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adresy`
--

CREATE TABLE `adresy` (
  `adres_id` int(11) NOT NULL,
  `miasto` varchar(40) NOT NULL,
  `ulica` varchar(50) NOT NULL,
  `kod_pocztowy` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `adresy`
--

INSERT INTO `adresy` (`adres_id`, `miasto`, `ulica`, `kod_pocztowy`) VALUES
(1, '1', '1', '1'),
(2, 'Gdańsk', '123', '12-123'),
(3, '123213', '12321', '111111'),
(4, '123213', '12321321', '12-111'),
(5, '123', '1234', '12-123'),
(6, 'Katowice', '', ''),
(7, 'Adr1', 'Ul1', '12-121'),
(8, '123', '123', '12-345'),
(9, 'Adr1', '12321', '12-111'),
(10, 'Adr1', 'Ul1', '12-321'),
(11, '123412', '12342315', '00-878'),
(12, '123', '', ''),
(13, '123', '123', '41-212'),
(14, 'Katowice', 'Poniatowskiego', '44-222'),
(15, 'Gdańsk', '123', '12-123'),
(16, 'Warszawa', 'Poniatowskiego', '44-222'),
(17, 'Adr1', '123', '12-111');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adresy_paczkomatów`
--

CREATE TABLE `adresy_paczkomatów` (
  `adres_id` int(11) NOT NULL,
  `paczkomat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `adresy_paczkomatów`
--

INSERT INTO `adresy_paczkomatów` (`adres_id`, `paczkomat_id`) VALUES
(14, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adresy_użytkowników`
--

CREATE TABLE `adresy_użytkowników` (
  `adres_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `ukryty` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `adresy_użytkowników`
--

INSERT INTO `adresy_użytkowników` (`adres_id`, `użytkownik_id`, `ukryty`) VALUES
(2, 1, 1),
(3, 1, 1),
(4, 1, 1),
(4, 3, 1),
(5, 1, 0),
(6, 1, 1),
(7, 1, 1),
(8, 1, 0),
(9, 1, 0),
(10, 1, 0),
(11, 1, 1),
(12, 1, 1),
(13, 1, 1),
(14, 3, 0),
(15, 3, 0),
(16, 3, 0),
(17, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `historia_zamówień`
--

CREATE TABLE `historia_zamówień` (
  `historia_id` int(11) NOT NULL,
  `przesyłka_id` int(11) NOT NULL,
  `status` enum('oczekuje','w_drodze','dostarczona') NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `paczkomaty`
--

CREATE TABLE `paczkomaty` (
  `paczkomat_id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `maksymalna_pojemność` smallint(6) NOT NULL,
  `aktualna_pojemność` smallint(6) NOT NULL,
  `dostępność` enum('dostępny','niedostępny') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `paczkomaty`
--

INSERT INTO `paczkomaty` (`paczkomat_id`, `nazwa`, `maksymalna_pojemność`, `aktualna_pojemność`, `dostępność`) VALUES
(1, 'KAT03', 40, 0, 'dostępny');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy_dane`
--

CREATE TABLE `pracownicy_dane` (
  `pracownik_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `pesel` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `przesyłki`
--

CREATE TABLE `przesyłki` (
  `przesyłka_id` int(11) NOT NULL,
  `rozmiary` enum('mała','średnia','duża') NOT NULL,
  `waga` int(11) NOT NULL,
  `typ` enum('ekspres','standard') NOT NULL,
  `adres_id` int(11) DEFAULT NULL,
  `typ_przesyłki` enum('prywatny','paczkomat') NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `aktualny_status` enum('oczekuje','w_drodze','dostarczona') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `przesyłki`
--

INSERT INTO `przesyłki` (`przesyłka_id`, `rozmiary`, `waga`, `typ`, `adres_id`, `typ_przesyłki`, `użytkownik_id`, `aktualny_status`) VALUES
(2, 'duża', 12, 'standard', 15, 'prywatny', 2, 'oczekuje'),
(3, 'duża', 66, 'standard', 10, 'prywatny', 1, 'oczekuje'),
(4, 'duża', 66, 'standard', 17, 'prywatny', 1, 'oczekuje'),
(5, 'duża', 77, 'standard', 14, 'prywatny', 3, 'oczekuje'),
(6, 'duża', 77, 'standard', 14, 'prywatny', 3, 'oczekuje'),
(7, 'średnia', 40, 'standard', 14, 'paczkomat', 3, 'oczekuje'),
(8, 'mała', 12, 'standard', 14, 'paczkomat', 3, 'oczekuje');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `użytkownicy`
--

CREATE TABLE `użytkownicy` (
  `użytkownik_id` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `haslo_hash` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `imie` varchar(40) NOT NULL,
  `nazwisko` varchar(40) NOT NULL,
  `nr_telefonu` varchar(15) NOT NULL,
  `rola` enum('admin','klient','pracownik') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `użytkownicy`
--

INSERT INTO `użytkownicy` (`użytkownik_id`, `login`, `haslo_hash`, `email`, `imie`, `nazwisko`, `nr_telefonu`, `rola`) VALUES
(1, 'Kowal', '$2y$10$RZXzyiFdg8/2GAN2zxfH4eze45eQYZtGUPlF5b8SJisQDmh3PYC56', '123@123', '1234', '1234', '123456789', 'klient'),
(2, 'PatKowal', '$2y$10$or6osp4zRzGl6L6Rf/FmseonjeBlgwWOKHd50qSPLoFMuZ3PO9JPe', '123123@1', '1234', '1234', '123456789', 'klient'),
(3, 'PatKowal', '$2y$10$51ZOla9lJvC1zae05NHBX.had4BM1R1.him8iImO871iKz0QirXJq', '1234@1234', 'Patryk', 'Kowal', '123456789', 'admin');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `adresy`
--
ALTER TABLE `adresy`
  ADD PRIMARY KEY (`adres_id`);

--
-- Indeksy dla tabeli `adresy_paczkomatów`
--
ALTER TABLE `adresy_paczkomatów`
  ADD PRIMARY KEY (`adres_id`,`paczkomat_id`),
  ADD KEY `paczkomat_id` (`paczkomat_id`);

--
-- Indeksy dla tabeli `adresy_użytkowników`
--
ALTER TABLE `adresy_użytkowników`
  ADD PRIMARY KEY (`adres_id`,`użytkownik_id`),
  ADD KEY `adresy_uzytkownik_fk_uzytkownik` (`użytkownik_id`);

--
-- Indeksy dla tabeli `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  ADD PRIMARY KEY (`historia_id`),
  ADD KEY `przesyłka_id` (`przesyłka_id`);

--
-- Indeksy dla tabeli `paczkomaty`
--
ALTER TABLE `paczkomaty`
  ADD PRIMARY KEY (`paczkomat_id`);

--
-- Indeksy dla tabeli `pracownicy_dane`
--
ALTER TABLE `pracownicy_dane`
  ADD PRIMARY KEY (`pracownik_id`),
  ADD UNIQUE KEY `użytkownik_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `przesyłki`
--
ALTER TABLE `przesyłki`
  ADD PRIMARY KEY (`przesyłka_id`),
  ADD KEY `adres_id` (`adres_id`),
  ADD KEY `klient_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `użytkownicy`
--
ALTER TABLE `użytkownicy`
  ADD PRIMARY KEY (`użytkownik_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adresy`
--
ALTER TABLE `adresy`
  MODIFY `adres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  MODIFY `historia_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paczkomaty`
--
ALTER TABLE `paczkomaty`
  MODIFY `paczkomat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pracownicy_dane`
--
ALTER TABLE `pracownicy_dane`
  MODIFY `pracownik_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `przesyłki`
--
ALTER TABLE `przesyłki`
  MODIFY `przesyłka_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `użytkownicy`
--
ALTER TABLE `użytkownicy`
  MODIFY `użytkownik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adresy_paczkomatów`
--
ALTER TABLE `adresy_paczkomatów`
  ADD CONSTRAINT `adresy_paczkomatów_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_paczkomatów_ibfk_2` FOREIGN KEY (`paczkomat_id`) REFERENCES `paczkomaty` (`paczkomat_id`);

--
-- Constraints for table `adresy_użytkowników`
--
ALTER TABLE `adresy_użytkowników`
  ADD CONSTRAINT `adresy_uzytkownik_fk_adres` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_uzytkownik_fk_uzytkownik` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`),
  ADD CONSTRAINT `adresy_użytkowników_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_użytkowników_ibfk_2` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

--
-- Constraints for table `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  ADD CONSTRAINT `historia_zamówień_ibfk_1` FOREIGN KEY (`przesyłka_id`) REFERENCES `przesyłki` (`przesyłka_id`);

--
-- Constraints for table `pracownicy_dane`
--
ALTER TABLE `pracownicy_dane`
  ADD CONSTRAINT `pracownicy_dane_ibfk_1` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `przesyłki`
--
ALTER TABLE `przesyłki`
  ADD CONSTRAINT `przesyłki_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `przesyłki_ibfk_2` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
