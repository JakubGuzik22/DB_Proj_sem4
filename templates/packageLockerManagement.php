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
    echo "Zaloguj się, aby zobaczyć paczkomaty.";
    exit;
}

$sql = "
    SELECT 
    p.paczkomat_id, 
    p.nazwa, 
    p.maksymalna_pojemność, 
    p.aktualna_pojemność, 
    p.dostępność
    FROM paczkomaty p
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Błąd zapytania.";
    mysqli_close($conn);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    echo '<div class="locker-container">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="locker-block">';
        echo '<div class="locker-info">';
        echo 'Nazwa: ' . htmlspecialchars($row["nazwa"]) . ', ';
        echo 'Pojemność: ' . htmlspecialchars($row["aktualna_pojemność"]) . '/' . htmlspecialchars($row["maksymalna_pojemność"]) . ', ';
        echo 'Dostępność: ' . htmlspecialchars($row["dostępność"]);
        echo '</div>';
        echo '<div class="locker-actions">';
        echo '<button class="btn-edit" data-paczkomatId="' . htmlspecialchars($row["paczkomat_id"]) . '">Edytuj paczkomat</button> ';
        echo '<button class="btn-delete" data-paczkomatId="' . htmlspecialchars($row["paczkomat_id"]) . '">Zmień dostępność</button>';
        echo '</div>';
        echo '</div>';
    }
    echo '<div class="locker-block">';
    echo '<button class="btn-edit" onclick="loadLockerForm()">Dodaj paczkomat</button>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div id="no-locker-message">';
    echo 'Brak dostępnych paczkomatów.';
    echo '<button class="btn-edit" onclick="loadLockerForm()">Dodaj paczkomat</button>';
    echo '</div>';
}

echo '<footer>';
echo '<button onclick="window.location.href=\'/\'">Wróć do strony głównej</button>';
echo '</footer>';

mysqli_close($conn);
?>
