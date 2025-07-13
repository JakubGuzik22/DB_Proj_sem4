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
    echo "Zaloguj się, aby zobaczyć użytkowników.";
    exit;
}

$sql = "SELECT użytkownik_id, login, email, rola FROM użytkownicy ORDER BY login";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Błąd zapytania.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo '<div class="users-list">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="user-block">';

        echo '<div>';
        echo 'Login: ' . htmlspecialchars($row['login']) . ', ';
        echo 'Email: ' . htmlspecialchars($row['email']) . ', ';
        echo 'Rola: ' . htmlspecialchars($row['rola']) . '.';
        echo '</div>';

        echo '<button class="btn-change-role" data-userid="' . (int)$row['użytkownik_id'] . '">Zmień rolę</button>';

        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>Brak użytkowników do wyświetlenia.</p>';
}
?>
