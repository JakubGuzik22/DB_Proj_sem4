<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby dodać/edytować paczkomat.";
    exit;
}

$paczkomatId = $_POST['paczkomat_id'] ?? null;
$nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : null;
$maksymalna_pojemnosc = isset($_POST['maksymalna_pojemnosc']) ? (int)$_POST['maksymalna_pojemnosc'] : null;
$adresId = isset($_POST['adres_id']) ? (int)$_POST['adres_id'] : null;

if (!$nazwa || !$maksymalna_pojemnosc || !$adresId) {
    echo "Błąd: wszystkie pola są wymagane.";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą danych.";
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

$aktualna_pojemnosc = 0;
if ($paczkomatId) {
    $sql = "SELECT aktualna_pojemność FROM paczkomaty WHERE paczkomat_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $paczkomatId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $aktualna_pojemnosc_db);
    if (mysqli_stmt_fetch($stmt)) {
        $aktualna_pojemnosc = (int)$aktualna_pojemnosc_db;
    }
    mysqli_stmt_close($stmt);

    if ($maksymalna_pojemnosc < $aktualna_pojemnosc) {
        echo "Błąd: maksymalna pojemność nie może być mniejsza niż aktualna ($aktualna_pojemnosc).";
        mysqli_close($conn);
        exit;
    }
}

if ($maksymalna_pojemnosc == $aktualna_pojemnosc) {
    $ukryty = 1;
    $dostepnosc = 'niedostępny';
} else {
    $ukryty = 0;
    $dostepnosc = 'dostępny';
}

if ($paczkomatId) {
    $sql = "UPDATE paczkomaty SET nazwa = ?, maksymalna_pojemność = ?, aktualna_pojemność = ?, dostępność = ? WHERE paczkomat_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "siisi", $nazwa, $maksymalna_pojemnosc, $aktualna_pojemnosc, $dostepnosc, $paczkomatId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    $sql = "INSERT INTO paczkomaty (nazwa, maksymalna_pojemność, aktualna_pojemność, dostępność) VALUES (?, ?, 0, 'dostępny')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nazwa, $maksymalna_pojemnosc);
    $result = mysqli_stmt_execute($stmt);
    $paczkomatId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}

if ($result) {
    $sql = "SELECT COUNT(*) FROM adresy_paczkomatów WHERE paczkomat_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $paczkomatId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        $sql = "UPDATE adresy_paczkomatów SET adres_id = ?, ukryty = ? WHERE paczkomat_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $adresId, $ukryty, $paczkomatId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $sql = "INSERT INTO adresy_paczkomatów (adres_id, paczkomat_id, ukryty) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $adresId, $paczkomatId, $ukryty);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
echo $result ? "OK" : "Błąd zapisu paczkomatu.";
