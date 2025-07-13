<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby dodać/edytować paczkomat.";
    exit;
}

$paczkomatId = $_POST['paczkomat_id'] ?? null;
$nazwa = isset($_POST['nazwa']) && trim($_POST['nazwa']) !== '' ? trim($_POST['nazwa']) : null;
$maksymalna_pojemnosc = isset($_POST['maksymalna_pojemnosc']) ? (int)$_POST['maksymalna_pojemnosc'] : null;
$adresId = isset($_POST['adres_id']) ? (int)$_POST['adres_id'] : null;
$dostepnosc = $_POST['dostepnosc'] ?? 'dostępny';

if ($nazwa === null || $maksymalna_pojemnosc === null || $adresId === null) {
    echo "Błąd: wszystkie pola są wymagane.";
    exit;
}

$ukryty = ($dostepnosc === 'niedostępny') ? 1 : 0;

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą danych.";
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

$aktualna_ilosc = 0;
if ($paczkomatId) {
    $sql_check = "SELECT `aktualna_pojemność` FROM `paczkomaty` WHERE `paczkomat_id` = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $paczkomatId);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_bind_result($stmt_check, $aktualna_pojemnosc_db);
    if (mysqli_stmt_fetch($stmt_check)) {
        $aktualna_ilosc = (int)$aktualna_pojemnosc_db;
    }
    mysqli_stmt_close($stmt_check);
}

if ($maksymalna_pojemnosc < $aktualna_ilosc) {
    echo "Błąd: maksymalna pojemność nie może być mniejsza niż aktualna ($aktualna_ilosc).";
    mysqli_close($conn);
    exit;
}

if ($paczkomatId) {
    $sql_update = "UPDATE `paczkomaty` SET `nazwa` = ?, `maksymalna_pojemność` = ?, `aktualna_pojemność` = ? WHERE `paczkomat_id` = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "siii", $nazwa, $maksymalna_pojemnosc, $aktualna_ilosc, $paczkomatId);
    $result = mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
} else {
    $sql_insert = "INSERT INTO `paczkomaty` (`nazwa`, `maksymalna_pojemność`, `aktualna_pojemność`, `dostępność`) VALUES (?, ?, 0, 'dostępny')";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "si", $nazwa, $maksymalna_pojemnosc);
    $result = mysqli_stmt_execute($stmt_insert);
    $paczkomatId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt_insert);
}

if ($result && $adresId) {
    $sql_check_link = "SELECT COUNT(*) FROM `adresy_paczkomatów` WHERE `paczkomat_id` = ?";
    $stmt_check_link = mysqli_prepare($conn, $sql_check_link);
    mysqli_stmt_bind_param($stmt_check_link, "i", $paczkomatId);
    mysqli_stmt_execute($stmt_check_link);
    mysqli_stmt_bind_result($stmt_check_link, $count);
    mysqli_stmt_fetch($stmt_check_link);
    mysqli_stmt_close($stmt_check_link);

    if ($count > 0) {
        $sql_update_link = "UPDATE `adresy_paczkomatów` SET `adres_id` = ?, `ukryty` = ? WHERE `paczkomat_id` = ?";
        $stmt_update_link = mysqli_prepare($conn, $sql_update_link);
        mysqli_stmt_bind_param($stmt_update_link, "iii", $adresId, $ukryty, $paczkomatId);
        mysqli_stmt_execute($stmt_update_link);
        mysqli_stmt_close($stmt_update_link);
    } else {
        $sql_insert_link = "INSERT INTO `adresy_paczkomatów` (`adres_id`, `paczkomat_id`, `ukryty`) VALUES (?, ?, ?)";
        $stmt_insert_link = mysqli_prepare($conn, $sql_insert_link);
        mysqli_stmt_bind_param($stmt_insert_link, "iii", $adresId, $paczkomatId, $ukryty);
        mysqli_stmt_execute($stmt_insert_link);
        mysqli_stmt_close($stmt_insert_link);
    }
}

$dostepnosc_status = ($ukryty === 1) ? 'niedostępny' : 'dostępny';
$sql_update_dostepnosc = "UPDATE `paczkomaty` SET `dostępność` = ? WHERE `paczkomat_id` = ?";
$stmt_dostepnosc = mysqli_prepare($conn, $sql_update_dostepnosc);
mysqli_stmt_bind_param($stmt_dostepnosc, "si", $dostepnosc_status, $paczkomatId);
mysqli_stmt_execute($stmt_dostepnosc);
mysqli_stmt_close($stmt_dostepnosc);

mysqli_close($conn);
echo $result ? "OK" : "Błąd zapisu paczkomatu.";
