<?php
    session_start();
    $isLoggedIn = isset($_SESSION['email']);
    mysqli_report(MYSQLI_REPORT_OFF);
	@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
	if (!$conn) {
        http_response_code(500);
        echo "Bład połączenia z bazą";
        exit;
	}
    mysqli_set_charset($conn, "utf8");
    $email = trim($_SESSION['email']);
	$sql = "SELECT * FROM `użytkownicy` WHERE `email` = '$email'";
    $result = mysqli_query($conn, $sql);
    if(!$result) {
		echo "Błąd zapytania";
		exit;
	}
	$row = mysqli_fetch_assoc($result);
	@$login = $row['login'];
	@$imie = $row['imie'];
	@$nazwisko = $row['nazwisko'];
	@$nr_telefonu = $row['nr_telefonu'];
	mysqli_close($conn);
?>

<div class="change-settings-container">
    <label for='firstName'>Login: <?= htmlspecialchars($login) ?></label>
    <label for='firstName'>Imię: <?= htmlspecialchars($imie) ?></label>
    <label for="lastName">Nazwisko: <?= htmlspecialchars($nazwisko) ?></label>
    <label for="phone">Nr. Telefonu: <?= htmlspecialchars($nr_telefonu) ?></label>
    <div class="buttons">
        <button type="submit" class="change-btn" onclick="event.preventDefault(); loadForm('form_change_settings')">Zmień dane osobiste</button>
    </div>
</div>

<footer>
    <button onclick="window.location.href='/'">Wróć do strony głównej</button>
</footer>