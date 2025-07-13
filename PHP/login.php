<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim($_POST['email']);
    $haslo = $_POST['password'];

	if(empty($email) || empty($haslo)) {
		echo "Brakuje danych logowania";
		exit;
	}
	mysqli_report(MYSQLI_REPORT_OFF);
	@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
	if (!$conn) {
            echo "Bład połączenia z bazą";
            exit;
	}
	$email = mysqli_real_escape_string($conn, $email);
	$sql = "SELECT * FROM `użytkownicy` WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

	if(!$result) {
		echo "Błąd zapytania";
		exit;
	}
	if(mysqli_num_rows($result) == 0){
		echo "Nieprawidłowy email";
		exit;
	}
	$row = mysqli_fetch_assoc($result);
	if (password_verify($haslo, $row['haslo_hash'])){
		session_start();
		$_SESSION['email'] = $row['email'];
		echo "OK";
	} else {
		echo "Niepoprawne hasło";
	}
	mysqli_close($conn);
}

