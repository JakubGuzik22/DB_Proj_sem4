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

$sql = "UPDATE adresy_paczkomatów SET ukryty = 1 WHERE paczkomat_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $paczkomatId);
if (mysqli_stmt_execute($stmt)) {
    echo "OK";
} else {
    echo "Błąd aktualizacji";
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
