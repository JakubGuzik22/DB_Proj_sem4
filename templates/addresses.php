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
    echo "Zaloguj się, aby zobaczyć adresy.";
    exit;
}

$email = trim($_SESSION['email']);

$sql = "
    SELECT DISTINCT
        a.adres_id,
        a.miasto,
        a.ulica,
        a.kod_pocztowy
    FROM użytkownicy u
    JOIN adresy_użytkowników au ON u.użytkownik_id = au.użytkownik_id
    JOIN adresy a ON au.adres_id = a.adres_id
    WHERE u.email = ? AND au.ukryty = 0
";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Błąd zapytania.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="address-container">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="address-block">';
        echo '<div class="address-info">';
        echo 'Miasto: ' . htmlspecialchars($row["miasto"]) . ', ';
        echo 'Ulica: ' . htmlspecialchars($row["ulica"]) . ', ';
        echo 'Kod pocztowy: ' . htmlspecialchars($row["kod_pocztowy"]);
        echo '</div>';
        echo '<div class="address-actions">';
        echo '<button class="btn-edit" data-adresid="' . htmlspecialchars($row["adres_id"]) . '">Zmień</button> ';
        echo '<button class="btn-delete" data-adresid="' . htmlspecialchars($row["adres_id"]) . '">Usuń</button>';
        echo '</div>';
        echo '</div>';
    }
    echo '<div class="address-block">';
    echo '<button class="btn-edit" onclick="loadAddressForm()">Dodaj kolejny adres</button>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div id="no-address-message">';
    echo 'Wygląda na to, że nie masz jeszcze dodanego adresu!';
    echo '<button class="btn-edit" onclick="loadAddressForm()">Dodaj adres</button>';
    echo '</div>';
}

echo '<footer>';
echo '<button onclick="window.location.href=\'/\'">Wróć do strony głównej</button>';
echo '</footer>';

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
