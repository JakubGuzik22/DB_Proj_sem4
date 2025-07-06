<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    http_response_code(500);
    echo "Błąd połączenia z bazą";
    exit;
}
mysqli_set_charset($conn, "utf8");

if (!isset($_SESSION['email'])) {
    echo "Brak sesji";
    exit;
}

$adresId = intval($_POST['adres_id'] ?? 0);
if ($adresId <= 0) {
    echo "Nieprawidłowy ID adresu";
    exit;
}

$email = $_SESSION['email'];

$sql = "
    UPDATE adresy_użytkowników au
    JOIN użytkownicy u ON au.użytkownik_id = u.użytkownik_id
    SET au.ukryty = 1
    WHERE au.adres_id = ? AND u.email = ?
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $adresId, $email);
if (mysqli_stmt_execute($stmt)) {
    echo "OK";
} else {
    echo "Błąd usuwania";
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
