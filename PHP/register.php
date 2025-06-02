<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $imie = trim($_POST['first-name']);
    $nazwisko = trim($_POST['last-name']);
    $nr_telefonu = trim($_POST['phone']);
    $haslo = $_POST['password'];

    if ($login && $email && $imie && $nazwisko && $nr_telefonu && $haslo) {
        mysqli_report(MYSQLI_REPORT_OFF);
        @$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
        if (!$conn) {
            http_response_code(500);
            echo "Bład połączenia z bazą";
            exit;
        }

        $login = mysqli_real_escape_string($conn, $login);
        $email = mysqli_real_escape_string($conn, $email);
        $imie = mysqli_real_escape_string($conn, $imie);
        $nazwisko = mysqli_real_escape_string($conn, $nazwisko);
        $nr_telefonu = mysqli_real_escape_string($conn, $nr_telefonu);

        $sqlCheck = "SELECT użytkownik_id FROM użytkownicy WHERE email='$email'";
        $result = mysqli_query($conn, $sqlCheck);
        if (!$result) {
            echo "Błąd zapytania";
            mysqli_close($conn);
            exit;
            // die("Błąd zapytania: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
            echo "Użytkownik z tym adresem email już istnieje";
            mysqli_close($conn);
            exit;
        }
        $haslo_hash = password_hash($haslo, PASSWORD_BCRYPT);
        $haslo_hash = mysqli_real_escape_string($conn, $haslo_hash);

        $sqlInsert = "INSERT INTO użytkownicy (login, haslo_hash, email, imie, nazwisko, nr_telefonu, rola) 
        VALUES ('$login', '$haslo_hash', '$email', '$imie', '$nazwisko', '$nr_telefonu', 'klient')";
        if (!mysqli_query($conn, $sqlInsert)) {
            die("Błąd dodawania użytkownika: " . mysqli_error($conn));
        }
    }
    mysqli_close($conn);
    echo "OK";
    exit;
}