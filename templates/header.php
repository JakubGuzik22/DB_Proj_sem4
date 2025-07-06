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
    @$email = $_SESSION['email'];
	$sql = "SELECT login FROM `użytkownicy` WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    if(!$result) {
		echo "Błąd zapytania";
		exit;
	}
	$row = mysqli_fetch_assoc($result);
	@$login = $row['login'];
	mysqli_close($conn);
?>

<header class="header">
    <div class="logo">
        <img src="../resources/logo.png" alt="Logo firmy">
    </div>
    <div class="header-right">
        <?php if ($isLoggedIn): ?>
            <nav class="nav">
                <a href="/">Nadaj paczkę</a>
                <a href="/">Moje paczki</a>
            </nav>
        <?php endif; ?>
        <div class="user-box">
            <?php if ($isLoggedIn): ?>
                <form method="post">    
                    <select name="user_action">
                        <option disabled selected hidden>Zalogowano jako: <?= htmlspecialchars($login) ?></option>
                        <option value="addresses">Moje Adresy</option>
                        <option value="settings">Ustawienia Konta</option>
                        <option value="logout">Wyloguj</option>
                    </select>
                </form>
            <?php else: ?>
                <button onclick="event.preventDefault(); loadForm('formLogin')">Zaloguj się</button>
            <?php endif; ?>
        </div>
    </div>
</header>