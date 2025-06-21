<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        echo "Zaloguj się, aby dodać/edytować adres.";
        exit;
    }

    $adresId = isset($_POST['adres_id']) ? $_POST['adres_id'] : null;
    $miasto = $_POST['miasto'] ?? '';
    $ulica = $_POST['ulica'] ?? '';
    $kod_pocztowy = $_POST['kod_pocztowy'] ?? '';

    $conn = mysqli_connect("localhost", "root", "", "firmakurierska");
    if (!$conn) {
        http_response_code(500);
        echo "Błąd połączenia z bazą";
        exit;
    }
    mysqli_set_charset($conn, "utf8");

    $email = trim($_SESSION['email']);
    $sql = "SELECT użytkownik_id FROM użytkownicy WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "Błąd zapytania";
        exit;
    }
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        echo "Nie znaleziono użytkownika";
        exit;
    }
    $uzytkownik_id = $row['użytkownik_id'];

    if ($adresId) {
        $adresId = (int)$adresId;
        $sql = "UPDATE adresy SET miasto='$miasto', ulica='$ulica', kod_pocztowy='$kod_pocztowy' WHERE adres_id=$adresId";
        $result = mysqli_query($conn, $sql);
    } else {
        $sql = "INSERT INTO adresy (miasto, ulica, kod_pocztowy) VALUES ('$miasto', '$ulica', '$kod_pocztowy')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $adresId = mysqli_insert_id($conn);
            $sql2 = "INSERT INTO adresy_użytkowników (adres_id, użytkownik_id) VALUES ($adresId, $uzytkownik_id)";
            mysqli_query($conn, $sql2);
        }
    }

    mysqli_close($conn);

    if ($result) {
        echo "OK";
    } else {
        echo "Błąd zapisu adresu";
    }
