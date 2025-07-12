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
	$sql = "SELECT login, rola FROM `użytkownicy` WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    if(!$result) {
		echo "Błąd zapytania";
		exit;
	}
	$row = mysqli_fetch_assoc($result);
	@$login = $row['login'];
    @$rola = $row['rola'];
	mysqli_close($conn);
?>

<header class="header">
    <div class="logo">
        <img src="../resources/logo.png" alt="Logo firmy">
    </div>
    <div class="header-right">
        <?php if ($isLoggedIn): ?>
            <nav class="nav">
                <a href="#" data-view="formPackage">Nadaj paczkę</a>
                <a href="#" data-view="myPackages">Moje paczki</a>
                <?php if ($rola == "pracownik" || $rola == "admin"): ?>
                <a href="#" data-view="packageManagement">Zarządzanie paczkami</a>
                <?php endif; ?>
                <?php if ($rola == "admin"): ?>
                <a href="#" data-view="userManagement">Zarządzanie użytkownikami</a>
                <?php endif; ?>
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