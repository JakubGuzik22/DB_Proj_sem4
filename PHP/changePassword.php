<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Nieprawidłowa metoda żądania.";
    exit;
}

if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    echo "Sesja wygasła. Zaloguj się ponownie.";
    exit;
}

$oldPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

if (!$oldPassword || !$newPassword || !$confirmPassword) {
    echo "Brak danych";
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo "Nowe hasło i potwierdzenie nie są takie same";
    exit;
}

$emailZalogowanego = trim($_SESSION['email']);

mysqli_report(MYSQLI_REPORT_OFF);
$polaczenie = mysqli_connect("localhost", "root", "", "firmakurierska");

if ($polaczenie->connect_error) {
    echo "Błąd połączenia z bazą danych.";
    exit;
}

$stmt = $polaczenie->prepare("SELECT haslo_hash, użytkownik_id FROM `użytkownicy` WHERE email = ?");
if (!$stmt) {
    echo "Błąd przygotowania zapytania.";
    $polaczenie->close();
    exit;
}
$stmt->bind_param('s', $emailZalogowanego);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Użytkownik nie znaleziony";
    $stmt->close();
    $polaczenie->close();
    exit;
}

$row = $result->fetch_assoc();
$hashFromDb = $row['haslo_hash'];
$userId = (int)$row['użytkownik_id'];
$stmt->close();

if (!password_verify($oldPassword, $hashFromDb)) {
    echo "Niepoprawne stare hasło";
    $polaczenie->close();
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_BCRYPT);

$stmtUpdate = $polaczenie->prepare("UPDATE `użytkownicy` SET haslo_hash = ? WHERE użytkownik_id = ?");
if (!$stmtUpdate) {
    echo "Błąd przygotowania zapytania aktualizacji.";
    $polaczenie->close();
    exit;
}
$stmtUpdate->bind_param('si', $newHash, $userId);

if ($stmtUpdate->execute()) {
    echo "OK";
} else {
    echo "Błąd aktualizacji hasła";
}

$stmtUpdate->close();
$polaczenie->close();
exit;
