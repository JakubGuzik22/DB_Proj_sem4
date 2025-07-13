<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą";
    exit;
}
mysqli_set_charset($conn, "utf8");

if (!isset($_SESSION['email'])) {
    echo "Brak sesji";
    exit;
}

$paczkomatId = intval($_POST['paczkomat_id'] ?? 0);
if ($paczkomatId <= 0) {
    echo "Nieprawidłowy locker_id";
    exit;
}

$sqlToggle = "UPDATE adresy_paczkomatów SET ukryty = NOT ukryty WHERE paczkomat_id = ?";
$stmtToggle = mysqli_prepare($conn, $sqlToggle);
mysqli_stmt_bind_param($stmtToggle, "i", $paczkomatId);
if (!mysqli_stmt_execute($stmtToggle)) {
    echo "Błąd aktualizacji ukrytego";
    mysqli_stmt_close($stmtToggle);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_close($stmtToggle);

$sqlSelect = "SELECT ukryty FROM adresy_paczkomatów WHERE paczkomat_id = ?";
$stmtSelect = mysqli_prepare($conn, $sqlSelect);
mysqli_stmt_bind_param($stmtSelect, "i", $paczkomatId);
mysqli_stmt_execute($stmtSelect);
mysqli_stmt_bind_result($stmtSelect, $ukryty);
mysqli_stmt_fetch($stmtSelect);
mysqli_stmt_close($stmtSelect);

$dostepnosc = ($ukryty == 1) ? 'niedostępny' : 'dostępny';
$sqlUpdate = "UPDATE paczkomaty SET dostępność = ? WHERE paczkomat_id = ?";
$stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
mysqli_stmt_bind_param($stmtUpdate, "si", $dostepnosc, $paczkomatId);
if (mysqli_stmt_execute($stmtUpdate)) {
    echo "OK";
} else {
    echo "Błąd aktualizacji dostępności";
}
mysqli_stmt_close($stmtUpdate);
mysqli_close($conn);
