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
        echo "Błąd zapytania: " . mysqli_error($conn);
		exit;
	}
	$row = mysqli_fetch_assoc($result);
	@$login = $row['login'];
	@$imie = $row['imie'];
	@$nazwisko = $row['nazwisko'];
	@$nr_telefonu = $row['nr_telefonu'];
	mysqli_close($conn);
?>

<div class="edit-profile-container">
    <label for='firstName'>Login: <?= htmlspecialchars($login) ?></label>
    <label for='firstName'>Imię: <?= htmlspecialchars($imie) ?></label>
    <label for="lastName">Nazwisko: <?= htmlspecialchars($nazwisko) ?></label>
    <label for="phone">Nr. Telefonu: <?= htmlspecialchars($nr_telefonu) ?></label>
    <div class="buttons">
        <button type="button" class="cancel-btn">Anuluj</button>
        <button type="submit" class="save-btn">Zmień dane osobiste</button>
    </div>
</div>