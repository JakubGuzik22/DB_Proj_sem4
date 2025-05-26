-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 26, 2025 at 03:07 PM
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
-- Struktura tabeli dla tabeli `administratorzy`
--

CREATE TABLE `administratorzy` (
  `admin_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `pesel` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adresy_paczkomatów`
--

CREATE TABLE `adresy_paczkomatów` (
  `adres_id` int(11) NOT NULL,
  `paczkomat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `adresy_użytkowników`
--

CREATE TABLE `adresy_użytkowników` (
  `adres_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

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
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE `klienci` (
  `klient_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL
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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy_firmowi`
--

CREATE TABLE `pracownicy_firmowi` (
  `pracownik_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `pesel` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

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
  `klient_id` int(11) NOT NULL,
  `aktualny_status` enum('oczekuje','w_drodze','dostarczona') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

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
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `administratorzy`
--
ALTER TABLE `administratorzy`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `użytkownik_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `adresy`
--
ALTER TABLE `adresy`
  ADD PRIMARY KEY (`adres_id`);

--
-- Indeksy dla tabeli `adresy_paczkomatów`
--
ALTER TABLE `adresy_paczkomatów`
  ADD PRIMARY KEY (`adres_id`),
  ADD KEY `paczkomat_id` (`paczkomat_id`);

--
-- Indeksy dla tabeli `adresy_użytkowników`
--
ALTER TABLE `adresy_użytkowników`
  ADD PRIMARY KEY (`adres_id`),
  ADD KEY `użytkownik_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  ADD PRIMARY KEY (`historia_id`),
  ADD KEY `przesyłka_id` (`przesyłka_id`);

--
-- Indeksy dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD PRIMARY KEY (`klient_id`),
  ADD KEY `użytkownik_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `paczkomaty`
--
ALTER TABLE `paczkomaty`
  ADD PRIMARY KEY (`paczkomat_id`);

--
-- Indeksy dla tabeli `pracownicy_firmowi`
--
ALTER TABLE `pracownicy_firmowi`
  ADD PRIMARY KEY (`pracownik_id`),
  ADD KEY `użytkownik_id` (`użytkownik_id`);

--
-- Indeksy dla tabeli `przesyłki`
--
ALTER TABLE `przesyłki`
  ADD PRIMARY KEY (`przesyłka_id`),
  ADD KEY `adres_id` (`adres_id`),
  ADD KEY `klient_id` (`klient_id`);

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
-- AUTO_INCREMENT for table `administratorzy`
--
ALTER TABLE `administratorzy`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adresy`
--
ALTER TABLE `adresy`
  MODIFY `adres_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  MODIFY `historia_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `klienci`
--
ALTER TABLE `klienci`
  MODIFY `klient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paczkomaty`
--
ALTER TABLE `paczkomaty`
  MODIFY `paczkomat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownicy_firmowi`
--
ALTER TABLE `pracownicy_firmowi`
  MODIFY `pracownik_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `przesyłki`
--
ALTER TABLE `przesyłki`
  MODIFY `przesyłka_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `użytkownicy`
--
ALTER TABLE `użytkownicy`
  MODIFY `użytkownik_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administratorzy`
--
ALTER TABLE `administratorzy`
  ADD CONSTRAINT `administratorzy_ibfk_1` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

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
  ADD CONSTRAINT `adresy_użytkowników_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_użytkowników_ibfk_2` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

--
-- Constraints for table `historia_zamówień`
--
ALTER TABLE `historia_zamówień`
  ADD CONSTRAINT `historia_zamówień_ibfk_1` FOREIGN KEY (`przesyłka_id`) REFERENCES `przesyłki` (`przesyłka_id`);

--
-- Constraints for table `klienci`
--
ALTER TABLE `klienci`
  ADD CONSTRAINT `klienci_ibfk_1` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

--
-- Constraints for table `pracownicy_firmowi`
--
ALTER TABLE `pracownicy_firmowi`
  ADD CONSTRAINT `pracownicy_firmowi_ibfk_1` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

--
-- Constraints for table `przesyłki`
--
ALTER TABLE `przesyłki`
  ADD CONSTRAINT `przesyłki_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `przesyłki_ibfk_2` FOREIGN KEY (`klient_id`) REFERENCES `klienci` (`klient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
