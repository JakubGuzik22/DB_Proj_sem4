
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `adresy` (
  `adres_id` int(11) NOT NULL,
  `miasto` varchar(40) NOT NULL,
  `ulica` varchar(50) NOT NULL,
  `kod_pocztowy` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE `adresy_paczkomatów` (
  `adres_id` int(11) NOT NULL,
  `paczkomat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE `adresy_użytkowników` (
  `adres_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `ukryty` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE `historia_zamówień` (
  `historia_id` int(11) NOT NULL,
  `przesyłka_id` int(11) NOT NULL,
  `status` enum('oczekuje','w_drodze','dostarczona') NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE `paczkomaty` (
  `paczkomat_id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `maksymalna_pojemność` smallint(6) NOT NULL,
  `aktualna_pojemność` smallint(6) NOT NULL,
  `dostępność` enum('dostępny','niedostępny') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

CREATE TABLE `pracownicy_dane` (
  `pracownik_id` int(11) NOT NULL,
  `użytkownik_id` int(11) NOT NULL,
  `pesel` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `użytkownicy` (`użytkownik_id`, `login`, `haslo_hash`, `email`, `imie`, `nazwisko`, `nr_telefonu`, `rola`) VALUES
(1, 'TestAdmin', '$2y$10$KE89rvGJzUJ.qf.7BwF2zOVmsikVGI3MQymMlZL0.130Bp6jKxeP.', 'test@admin', 'Test', 'Admin', '123456789', 'admin'),
(2, 'TestPracownik', '$2y$10$yvnkMgDWgx9u5/l6Yxdd/.uaE30JcP/rw8APIA7I6b02OHzmp1Tym', 'test@pracownik', 'Test', 'Pracownik', '123456789', 'pracownik'),
(3, 'TestKlient', '$2y$10$Ln68MFFt7FY2GnyhHGWbaOtUANL5W/5GQ22c315IhemLjHsv/5WGe', 'test@klient', 'Tester', 'Klient', '123456789', 'klient');

ALTER TABLE `adresy`
  ADD PRIMARY KEY (`adres_id`);

ALTER TABLE `adresy_paczkomatów`
  ADD PRIMARY KEY (`adres_id`,`paczkomat_id`),
  ADD KEY `paczkomat_id` (`paczkomat_id`);

ALTER TABLE `adresy_użytkowników`
  ADD PRIMARY KEY (`adres_id`,`użytkownik_id`),
  ADD KEY `adresy_uzytkownik_fk_uzytkownik` (`użytkownik_id`);

ALTER TABLE `historia_zamówień`
  ADD PRIMARY KEY (`historia_id`),
  ADD KEY `przesyłka_id` (`przesyłka_id`);

ALTER TABLE `paczkomaty`
  ADD PRIMARY KEY (`paczkomat_id`);

ALTER TABLE `pracownicy_dane`
  ADD PRIMARY KEY (`pracownik_id`),
  ADD UNIQUE KEY `użytkownik_id` (`użytkownik_id`);

ALTER TABLE `przesyłki`
  ADD PRIMARY KEY (`przesyłka_id`),
  ADD KEY `adres_id` (`adres_id`),
  ADD KEY `klient_id` (`użytkownik_id`);

ALTER TABLE `użytkownicy`
  ADD PRIMARY KEY (`użytkownik_id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `adresy`
  MODIFY `adres_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `historia_zamówień`
  MODIFY `historia_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `paczkomaty`
  MODIFY `paczkomat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `pracownicy_dane`
  MODIFY `pracownik_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `przesyłki`
  MODIFY `przesyłka_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `użytkownicy`
  MODIFY `użytkownik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `adresy_paczkomatów`
  ADD CONSTRAINT `adresy_paczkomatów_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_paczkomatów_ibfk_2` FOREIGN KEY (`paczkomat_id`) REFERENCES `paczkomaty` (`paczkomat_id`);

ALTER TABLE `adresy_użytkowników`
  ADD CONSTRAINT `adresy_uzytkownik_fk_adres` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_uzytkownik_fk_uzytkownik` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`),
  ADD CONSTRAINT `adresy_użytkowników_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `adresy_użytkowników_ibfk_2` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`);

ALTER TABLE `historia_zamówień`
  ADD CONSTRAINT `historia_zamówień_ibfk_1` FOREIGN KEY (`przesyłka_id`) REFERENCES `przesyłki` (`przesyłka_id`);

ALTER TABLE `pracownicy_dane`
  ADD CONSTRAINT `pracownicy_dane_ibfk_1` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `przesyłki`
  ADD CONSTRAINT `przesyłki_ibfk_1` FOREIGN KEY (`adres_id`) REFERENCES `adresy` (`adres_id`),
  ADD CONSTRAINT `przesyłki_ibfk_2` FOREIGN KEY (`użytkownik_id`) REFERENCES `użytkownicy` (`użytkownik_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;