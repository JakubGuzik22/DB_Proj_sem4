<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");

if (!$conn) {
    http_response_code(500);
    echo "Błąd połączenia z bazą";
    exit;
}

mysqli_set_charset($conn, "utf8");

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby zobaczyć przesyłki.";
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
        p.przesyłka_id,
        p.rozmiary,
        p.waga,
        p.typ,
        a.miasto,
        a.ulica,
        a.kod_pocztowy,
        p.aktualny_status,
        p.typ_przesyłki,
        pm.nazwa AS paczkomat_nazwa
    FROM przesyłki p
    LEFT JOIN adresy a ON p.adres_id = a.adres_id
    LEFT JOIN adresy_paczkomatów ap ON ap.adres_id = p.adres_id
    LEFT JOIN paczkomaty pm ON pm.paczkomat_id = ap.paczkomat_id
    WHERE p.użytkownik_id = ?
    ORDER BY p.przesyłka_id DESC
";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Błąd zapytania przesyłek.";
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
        echo 'Waga: ' . htmlspecialchars($row["waga"]) . ', ';
        echo 'Typ: ' . htmlspecialchars($row["typ"]) . ', ';

        if ($row["typ_przesyłki"] === 'paczkomat') {
            echo 'Paczkomat: ' . htmlspecialchars($row["paczkomat_nazwa"]) . ', ';
        } else {
            echo 'Adres: ' . htmlspecialchars($row["miasto"]) . ', ' . htmlspecialchars($row["ulica"]) . ', ' . htmlspecialchars($row["kod_pocztowy"]) . ', ';
        }

        echo 'Status: ' . htmlspecialchars($row["aktualny_status"]);
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo 'Brak przesyłek dla tego użytkownika.';
}

echo '<footer>';
echo '<button onclick="window.location.href=\'/\'">Wróć do strony głównej</button>';
echo '</footer>';

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
