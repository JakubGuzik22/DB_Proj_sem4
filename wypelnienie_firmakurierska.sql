
-- Wypełnianie tabel dla bazy danych firmakurierska

INSERT INTO użytkownicy (użytkownik_id, login, haslo_hash, email, imie, nazwisko, nr_telefonu, rola) VALUES
(1, 'admin1', 'hash1', 'admin1@example.com', 'Jan', 'Nowak', '123456789', 'admin'),
(2, 'klient1', 'hash2', 'klient1@example.com', 'Anna', 'Kowalska', '987654321', 'klient'),
(3, 'klient2', 'hash3', 'klient2@example.com', 'Piotr', 'Wiśniewski', '123123123', 'klient'),
(4, 'klient3', 'hash4', 'klient3@example.com', 'Katarzyna', 'Wójcik', '321321321', 'klient'),
(5, 'klient4', 'hash5', 'klient4@example.com', 'Tomasz', 'Kamiński', '456456456', 'klient'),
(6, 'prac1', 'hash6', 'prac1@example.com', 'Michał', 'Lewandowski', '789789789', 'pracownik'),
(7, 'prac2', 'hash7', 'prac2@example.com', 'Barbara', 'Zielińska', '741741741', 'pracownik'),
(8, 'klient5', 'hash8', 'klient5@example.com', 'Aleksandra', 'Szymańska', '852852852', 'klient'),
(9, 'admin2', 'hash9', 'admin2@example.com', 'Paweł', 'Dąbrowski', '963963963', 'admin'),
(10, 'klient6', 'hash10', 'klient6@example.com', 'Ewa', 'Król', '159159159', 'klient');

INSERT INTO administratorzy (admin_id, użytkownik_id, pesel) VALUES
(1, 1, '90010112345'),
(2, 9, '81020254321');

INSERT INTO klienci (klient_id, użytkownik_id) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 8),
(6, 10);

INSERT INTO pracownicy_firmowi (pracownik_id, użytkownik_id, pesel) VALUES
(1, 6, '85030365432'),
(2, 7, '92040498765');

INSERT INTO adresy (adres_id, miasto, ulica, kod_pocztowy) VALUES
(1, 'Warszawa', 'ul. Długa 10', '00-001'),
(2, 'Kraków', 'ul. Krótka 5', '30-002'),
(3, 'Wrocław', 'ul. Wąska 7', '50-003'),
(4, 'Gdańsk', 'ul. Szeroka 12', '80-004'),
(5, 'Poznań', 'ul. Prosta 3', '60-005'),
(6, 'Łódź', 'ul. Zielona 15', '90-006'),
(7, 'Lublin', 'ul. Jasna 9', '20-007'),
(8, 'Katowice', 'ul. Ciemna 2', '40-008'),
(9, 'Szczecin', 'ul. Główna 1', '70-009'),
(10, 'Bydgoszcz', 'ul. Kolejowa 4', '85-010');

INSERT INTO adresy_użytkowników (adres_id, użytkownik_id) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 8),
(6, 10);

INSERT INTO paczkomaty (paczkomat_id, nazwa, maksymalna_pojemność, aktualna_pojemność, dostępność) VALUES
(1, 'Paczkomat WAW001', 100, 50, 'dostępny'),
(2, 'Paczkomat KRK002', 80, 20, 'dostępny'),
(3, 'Paczkomat WRO003', 120, 100, 'niedostępny'),
(4, 'Paczkomat GDA004', 60, 10, 'dostępny'),
(5, 'Paczkomat POZ005', 90, 90, 'niedostępny'),
(6, 'Paczkomat LOD006', 70, 30, 'dostępny'),
(7, 'Paczkomat LUB007', 85, 85, 'dostępny'),
(8, 'Paczkomat KAT008', 100, 60, 'dostępny'),
(9, 'Paczkomat SZC009', 95, 95, 'niedostępny'),
(10, 'Paczkomat BYD010', 110, 10, 'dostępny');

INSERT INTO adresy_paczkomatów (adres_id, paczkomat_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10);

INSERT INTO przesyłki (przesyłka_id, rozmiary, waga, typ, adres_id, klient_id, aktualny_status) VALUES
(1, 'mała', 1, 'standard', 1, 1, 'oczekuje'),
(2, 'średnia', 3, 'ekspres', 2, 2, 'w_drodze'),
(3, 'duża', 5, 'standard', 3, 3, 'dostarczona'),
(4, 'mała', 2, 'ekspres', 4, 4, 'w_drodze'),
(5, 'średnia', 4, 'standard', 5, 5, 'oczekuje'),
(6, 'duża', 6, 'ekspres', 6, 6, 'dostarczona'),
(7, 'mała', 1, 'standard', 7, 1, 'w_drodze'),
(8, 'średnia', 2, 'ekspres', 8, 2, 'oczekuje'),
(9, 'duża', 6, 'standard', 9, 3, 'w_drodze'),
(10, 'mała', 1, 'ekspres', 10, 4, 'dostarczona');

INSERT INTO historia_zamówień (historia_id, przesyłka_id, status, data) VALUES
(1, 1, 'oczekuje', '2025-05-01'),
(2, 2, 'w_drodze', '2025-05-02'),
(3, 3, 'dostarczona', '2025-05-03'),
(4, 4, 'w_drodze', '2025-05-04'),
(5, 5, 'oczekuje', '2025-05-05'),
(6, 6, 'dostarczona', '2025-05-06'),
(7, 7, 'w_drodze', '2025-05-07'),
(8, 8, 'oczekuje', '2025-05-08'),
(9, 9, 'w_drodze', '2025-05-09'),
(10, 10, 'dostarczona', '2025-05-10');
