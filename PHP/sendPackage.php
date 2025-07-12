<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby nadać przesyłkę.";
    exit;
}

$rozmiar = $_POST['size'] ?? '';
$waga = trim($_POST['weight'] ?? '');
$typ = $_POST['type'] ?? '';
$adres_id = $_POST['address'] ?? '';

if (!in_array($rozmiar, ['mała', 'średnia', 'duża']) ||
    !in_array($typ, ['ekspres', 'standard']) ||
    !is_numeric($waga) || $waga <= 0 ||
    !is_numeric($adres_id) || $adres_id <= 0) {
    echo "Nieprawidłowe dane wejściowe.";
    exit;
}

$wagaInt = (int) round($waga);

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą danych.";
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

$email = trim($_SESSION['email']);
$sqlUser = "SELECT użytkownik_id FROM użytkownicy WHERE email = ?";
$stmtUser = mysqli_prepare($conn, $sqlUser);
if (!$stmtUser) {
    echo "Błąd przygotowania zapytania użytkownika.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmtUser, "s", $email);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $userId);
if (!mysqli_stmt_fetch($stmtUser)) {
    mysqli_stmt_close($stmtUser);
    mysqli_close($conn);
    echo "Nie znaleziono użytkownika.";
    exit;
}
mysqli_stmt_close($stmtUser);

$sqlInsert = "INSERT INTO przesyłki (rozmiary, waga, typ, adres_id, użytkownik_id, aktualny_status) VALUES (?, ?, ?, ?, ?, 'oczekuje')";
$stmtInsert = mysqli_prepare($conn, $sqlInsert);
if (!$stmtInsert) {
    echo "Błąd przygotowania zapytania INSERT.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmtInsert, "sisii", $rozmiar, $wagaInt, $typ, $adres_id, $userId);
if (mysqli_stmt_execute($stmtInsert)) {
    echo "OK";
} else {
    echo "Błąd zapisu przesyłki.";
}

mysqli_stmt_close($stmtInsert);
mysqli_close($conn);
