<?php
    session_start();
    $isLoggedIn = isset($_SESSION['email']);
    mysqli_report(MYSQLI_REPORT_OFF);
	@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
	if (!$conn) {
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
    <form method="post" id="changeSettingsForm">
      <label for="login">Login</label>
      <input type="text" id="login" name="login" placeholder="<?= htmlspecialchars($login) ?>">

      <label for="firstName">Imię</label>
      <input type="text" id="firstName" name="firstName" placeholder="<?= htmlspecialchars($imie) ?>">

      <label for="lastName">Nazwisko</label>
      <input type="text" id="lastName" name="lastName" placeholder="<?= htmlspecialchars($nazwisko) ?>">

      <label for="phone">Nr. Telefonu</label>
      <input type="tel" id="phone" name="phone" placeholder="<?= htmlspecialchars($nr_telefonu) ?>" pattern="[0-9]{3}[0-9]{3}[0-9]{3}">

      <div class="buttons">
        <button type="button" class="cancel-btn">Anuluj</button>
        <button type="submit" class="save-btn">Zmień dane osobiste</button>
      </div>
    </form>
</div>