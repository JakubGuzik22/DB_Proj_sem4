<?php
mysqli_report(MYSQLI_REPORT_OFF);
@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");

if (!$conn) {
    echo "Błąd połączenia z bazą";
    exit;
}

mysqli_set_charset($conn, "utf8");

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
        p.typ_przesyłki
    FROM przesyłki p
    LEFT JOIN adresy a ON p.adres_id = a.adres_id
    ORDER BY p.przesyłka_id DESC
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="packages-container">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="address-block">';
        echo '<div class="address-info">';
        echo 'Rozmiar: ' . htmlspecialchars($row["rozmiary"]) . ', ';
        echo 'Waga: ' . htmlspecialchars($row["waga"]) . ', ';
        echo 'Typ: ' . htmlspecialchars($row["typ"]) . ', ';
        echo 'Adres: ' . htmlspecialchars($row["miasto"]) . ', ' . htmlspecialchars($row["ulica"]) . ', ' . htmlspecialchars($row["kod_pocztowy"]) . ', ';
        echo 'Status: ' . htmlspecialchars($row["aktualny_status"]);
        echo '</div>';
        echo '<div class="address-actions">';
        if ($row["aktualny_status"] !== 'dostarczona') {
            echo '<button class="btn-edit-status" data-paczkaid="' . (int)$row["przesyłka_id"] . '">Edytuj status</button>';
        }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo 'Brak przesyłek w bazie.';
}

echo '<footer>';
echo '<button onclick="window.location.href=\'/\'">Wróć do strony głównej</button>';
echo '</footer>';

mysqli_close($conn);
?>
