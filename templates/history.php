<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");

if (!$conn) {
    echo "Błąd połączenia z bazą";
    exit;
}

mysqli_set_charset($conn, "utf8");

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby zobaczyć historię zamówień.";
    exit;
}

$email = trim($_SESSION['email']);

$sqlUser = "SELECT użytkownik_id FROM użytkownicy WHERE email = ?";
$stmtUser = mysqli_prepare($conn, $sqlUser);
if (!$stmtUser) {
    echo "Błąd zapytania użytkownika.";
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($stmtUser, "s", $email);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $userId);
if (!mysqli_stmt_fetch($stmtUser)) {
    echo "Nie znaleziono użytkownika.";
    mysqli_stmt_close($stmtUser);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_close($stmtUser);

$sql = "
    SELECT 
        h.status, h.data,
        p.rozmiary, p.waga, p.typ_przesyłki,
        a.miasto, a.ulica, a.kod_pocztowy
    FROM historia_zamówień h
    INNER JOIN przesyłki p ON h.przesyłka_id = p.przesyłka_id
    LEFT JOIN adresy a ON p.adres_id = a.adres_id
    WHERE p.użytkownik_id = ?
    ORDER BY h.data DESC
";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Błąd zapytania historii.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="packages-container">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="address-block">';
        echo '<div class="address-info">';
        echo 'Rozmiar: ' . htmlspecialchars($row["rozmiary"]) . ', ';
        echo 'Waga: ' . htmlspecialchars($row["waga"]) . ' kg, ';
        echo 'Typ: ' . htmlspecialchars($row["typ_przesyłki"]) . '<br>';
        echo 'Adres: ' . htmlspecialchars($row["miasto"]) . ', ' . htmlspecialchars($row["ulica"]) . ', ' . htmlspecialchars($row["kod_pocztowy"]) . '<br>';
        echo 'Status: ' . htmlspecialchars($row["status"]) . ', ';
        echo 'Data: ' . htmlspecialchars($row["data"]);
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo 'Brak historii zamówień.';
}

echo '<footer>';
echo '<button onclick="window.location.href=\'/\'">Wróć do strony głównej</button>';
echo '</footer>';

mysqli_stmt_close($stmt);
mysqli_close($conn);
