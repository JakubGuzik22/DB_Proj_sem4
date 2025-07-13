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

$userId = intval($_POST['user_id'] ?? 0);
$newRole = $_POST['role'] ?? '';

$validRoles = ['admin', 'klient', 'pracownik'];
if ($userId <= 0 || !in_array($newRole, $validRoles)) {
    echo "Nieprawidłowe dane";
    exit;
}

$sql = "UPDATE użytkownicy SET rola = ? WHERE użytkownik_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $newRole, $userId);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "OK";
    } else {
        echo "Brak zmian (rola już ustawiona lub użytkownik nie istnieje)";
    }
} else {
    echo "Błąd zmiany roli";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
