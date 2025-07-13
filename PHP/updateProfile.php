<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        echo "Sesja wygasła. Zaloguj się ponownie.";
        exit;
    }

    $emailZalogowanego = trim($_SESSION['email']);
    mysqli_report(MYSQLI_REPORT_OFF);
    $polaczenie = mysqli_connect("localhost", "root", "", "firmakurierska");

    if ($polaczenie->connect_error) {
        echo "Błąd połączenia z bazą danych.";
        exit;
    }

    $polaAktualizacji = [];
    $typyPowiazan = '';
    $wartosciPowiazan = [];

    if (isset($_POST['login']) && trim($_POST['login']) !== '') {
        $polaAktualizacji[] = "`login` = ?";
        $typyPowiazan .= 's';
        $wartosciPowiazan[] = trim($_POST['login']);
    }

    if (isset($_POST['firstName']) && trim($_POST['firstName']) !== '') {
        $polaAktualizacji[] = "`imie` = ?";
        $typyPowiazan .= 's';
        $wartosciPowiazan[] = trim($_POST['firstName']);
    }

    if (isset($_POST['lastName']) && trim($_POST['lastName']) !== '') {
        $polaAktualizacji[] = "`nazwisko` = ?";
        $typyPowiazan .= 's';
        $wartosciPowiazan[] = trim($_POST['lastName']);
    }

    if (isset($_POST['phone']) && trim($_POST['phone']) !== '') {
        $telefon = trim($_POST['phone']);
        $polaAktualizacji[] = "`nr_telefonu` = ?";
        $typyPowiazan .= 's';
        $wartosciPowiazan[] = $telefon;
    }

    if (empty($polaAktualizacji)) {
        echo "Brak danych do aktualizacji lub podane dane są puste.";
        $polaczenie->close();
        exit;
    }

    $zapytanie = "UPDATE `użytkownicy` SET " . implode(", ", $polaAktualizacji) . " WHERE `email` = ?";
    $typyPowiazan .= 's';
    $wartosciPowiazan[] = $emailZalogowanego;

    $stmt = $polaczenie->prepare($zapytanie);

    if (!$stmt) {
        echo "Błąd zapytania.";
        $polaczenie->close();
        exit;
    }

    $parametry = array_merge([$typyPowiazan], $wartosciPowiazan);
    $referencje = [];
    foreach ($parametry as $klucz => $wartosc) {
        $referencje[$klucz] = &$parametry[$klucz];
    }
    call_user_func_array([$stmt, 'bind_param'], $referencje);

    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Błąd wykonania zapytania.";
    }

    $stmt->close();
    $polaczenie->close();
} else {
    echo "Nieprawidłowa metoda żądania.";
}
