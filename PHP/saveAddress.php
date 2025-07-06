<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby dodać/edytować adres.";
    exit;
}

$adresId = $_POST['adres_id'] ?? null;
$miasto = isset($_POST['miasto']) && trim($_POST['miasto']) !== '' ? trim($_POST['miasto']) : null;
$ulica = isset($_POST['ulica']) && trim($_POST['ulica']) !== '' ? trim($_POST['ulica']) : null;
$kod_pocztowy = isset($_POST['kod_pocztowy']) && trim($_POST['kod_pocztowy']) !== '' ? trim($_POST['kod_pocztowy']) : null;

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    http_response_code(500);
    echo "Błąd połączenia z bazą danych.";
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

$email = trim($_SESSION['email']);
$stmt_user = mysqli_prepare($conn, "SELECT użytkownik_id FROM użytkownicy WHERE email = ?");
mysqli_stmt_bind_param($stmt_user, "s", $email);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$row_user = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_user);

if (!$row_user) {
    echo "Nie znaleziono użytkownika.";
    mysqli_close($conn);
    exit;
}
$uzytkownik_id = $row_user['użytkownik_id'];

$result = false;

if ($adresId) {
    $adresId = (int)$adresId;

    $sql_current = "SELECT miasto, ulica, kod_pocztowy FROM adresy WHERE adres_id = ?";
    $stmt_current = mysqli_prepare($conn, $sql_current);
    mysqli_stmt_bind_param($stmt_current, "i", $adresId);
    mysqli_stmt_execute($stmt_current);
    $result_current = mysqli_stmt_get_result($stmt_current);
    $row_current = mysqli_fetch_assoc($result_current);
    mysqli_stmt_close($stmt_current);

    if (!$row_current) {
        echo "Błąd: Adres nie istnieje.";
        mysqli_close($conn);
        exit;
    }

    if ($miasto === null) $miasto = $row_current['miasto'];
    if ($ulica === null) $ulica = $row_current['ulica'];
    if ($kod_pocztowy === null) $kod_pocztowy = $row_current['kod_pocztowy'];

    $sql_update = "UPDATE adresy SET miasto = ?, ulica = ?, kod_pocztowy = ? WHERE adres_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sssi", $miasto, $ulica, $kod_pocztowy, $adresId);
    $result = mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

} else {

    if ($miasto === null || $ulica === null || $kod_pocztowy === null) {
        echo "Błąd: Wszystkie pola (miasto, ulica, kod pocztowy) są wymagane.";
        mysqli_close($conn);
        exit;
    }

    $sql_check = "SELECT adres_id FROM adresy WHERE miasto = ? AND ulica = ? AND kod_pocztowy = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "sss", $miasto, $ulica, $kod_pocztowy);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check) {
        $adresId = (int)$row_check['adres_id'];

        $sql_check_link = "SELECT ukryty FROM adresy_użytkowników WHERE adres_id = ? AND użytkownik_id = ?";
        $stmt_check_link = mysqli_prepare($conn, $sql_check_link);
        mysqli_stmt_bind_param($stmt_check_link, "ii", $adresId, $uzytkownik_id);
        mysqli_stmt_execute($stmt_check_link);
        $result_check_link = mysqli_stmt_get_result($stmt_check_link);
        $row_link = mysqli_fetch_assoc($result_check_link);
        mysqli_stmt_close($stmt_check_link);

        if (!$row_link) {
            $sql_insert_link = "INSERT INTO adresy_użytkowników (adres_id, użytkownik_id) VALUES (?, ?)";
            $stmt_insert_link = mysqli_prepare($conn, $sql_insert_link);
            mysqli_stmt_bind_param($stmt_insert_link, "ii", $adresId, $uzytkownik_id);
            $result = mysqli_stmt_execute($stmt_insert_link);
            mysqli_stmt_close($stmt_insert_link);
        } elseif ((int)$row_link['ukryty'] === 1) {
            $sql_update_link = "UPDATE adresy_użytkowników SET ukryty = 0 WHERE adres_id = ? AND użytkownik_id = ?";
            $stmt_update_link = mysqli_prepare($conn, $sql_update_link);
            mysqli_stmt_bind_param($stmt_update_link, "ii", $adresId, $uzytkownik_id);
            $result = mysqli_stmt_execute($stmt_update_link);
            mysqli_stmt_close($stmt_update_link);
        } else {
            $result = true;
        }

    } else {
        $sql_insert = "INSERT INTO adresy (miasto, ulica, kod_pocztowy) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "sss", $miasto, $ulica, $kod_pocztowy);
        $result = mysqli_stmt_execute($stmt_insert);
        if ($result) {
            $adresId = mysqli_insert_id($conn);
            $sql_insert_user_address = "INSERT INTO adresy_użytkowników (adres_id, użytkownik_id) VALUES (?, ?)";
            $stmt_insert_user_address = mysqli_prepare($conn, $sql_insert_user_address);
            mysqli_stmt_bind_param($stmt_insert_user_address, "ii", $adresId, $uzytkownik_id);
            mysqli_stmt_execute($stmt_insert_user_address);
            mysqli_stmt_close($stmt_insert_user_address);
        }
        mysqli_stmt_close($stmt_insert);
    }
}

mysqli_close($conn);
echo $result ? "OK" : "Błąd zapisu adresu.";
