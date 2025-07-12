<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby nadać przesyłkę.";
    exit;
}

$rozmiar = $_POST['size'] ?? '';
$waga = trim($_POST['weight'] ?? '');
$typ = $_POST['type'] ?? '';
$adres_id = $_POST['address'] ?? '';
$typ_przesylki = $_POST['typ_przesyłki'] ?? '';

if (!in_array($rozmiar, ['mała', 'średnia', 'duża']) ||
    !in_array($typ, ['ekspres', 'standard']) ||
    !is_numeric($waga) || $waga <= 0 ||
    !is_numeric($adres_id) || $adres_id <= 0 ||
    !in_array($typ_przesylki, ['prywatny', 'paczkomat'])) {
    echo "Nieprawidłowe dane wejściowe.";
    exit;
}

$wagaInt = (int) round($waga);

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą danych.";
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

$email = trim($_SESSION['email']);
$sqlUser = "SELECT użytkownik_id FROM użytkownicy WHERE email = ?";
$stmtUser = mysqli_prepare($conn, $sqlUser);
if (!$stmtUser) {
    echo "Błąd zapytania użytkownika.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmtUser, "s", $email);
mysqli_stmt_execute($stmtUser);
mysqli_stmt_bind_result($stmtUser, $userId);
if (!mysqli_stmt_fetch($stmtUser)) {
    mysqli_stmt_close($stmtUser);
    mysqli_close($conn);
    echo "Nie znaleziono użytkownika.";
    exit;
}
mysqli_stmt_close($stmtUser);

$sqlInsert = "INSERT INTO przesyłki (rozmiary, waga, typ, adres_id, użytkownik_id, aktualny_status, typ_przesyłki) 
              VALUES (?, ?, ?, ?, ?, 'oczekuje', ?)";
$stmtInsert = mysqli_prepare($conn, $sqlInsert);
if (!$stmtInsert) {
    echo "Błąd zapytania INSERT.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmtInsert, "sisiss", $rozmiar, $wagaInt, $typ, $adres_id, $userId, $typ_przesylki);

if (mysqli_stmt_execute($stmtInsert)) {
    $przesylkaId = mysqli_insert_id($conn);

    $sqlHistory = "INSERT INTO historia_zamówień (przesyłka_id, status, data) VALUES (?, 'oczekuje', NOW())";
    $stmtHistory = mysqli_prepare($conn, $sqlHistory);
    if ($stmtHistory) {
        mysqli_stmt_bind_param($stmtHistory, "i", $przesylkaId);
        mysqli_stmt_execute($stmtHistory);
        mysqli_stmt_close($stmtHistory);
    }

    if ($typ_przesylki === 'paczkomat') {
        $sqlGetPaczkomat = "SELECT p.paczkomat_id, p.aktualna_pojemność, p.maksymalna_pojemność 
                            FROM paczkomaty p 
                            JOIN adresy_paczkomatów ap ON ap.paczkomat_id = p.paczkomat_id 
                            WHERE ap.adres_id = ?";
        $stmtGet = mysqli_prepare($conn, $sqlGetPaczkomat);
        if ($stmtGet) {
            mysqli_stmt_bind_param($stmtGet, "i", $adres_id);
            mysqli_stmt_execute($stmtGet);
            mysqli_stmt_bind_result($stmtGet, $paczkomatId, $aktualna, $maksymalna);
            if (mysqli_stmt_fetch($stmtGet)) {
                mysqli_stmt_close($stmtGet);

                $nowaIlosc = $aktualna + 1;

                $sqlUpdateIlosc = "UPDATE paczkomaty SET aktualna_pojemność = ? WHERE paczkomat_id = ?";
                $stmtUpdate = mysqli_prepare($conn, $sqlUpdateIlosc);
                if ($stmtUpdate) {
                    mysqli_stmt_bind_param($stmtUpdate, "ii", $nowaIlosc, $paczkomatId);
                    mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);
                }

                if ($nowaIlosc >= $maksymalna) {
                    $sqlSetNiedostepny = "UPDATE paczkomaty SET dostępność = 'niedostępny' WHERE paczkomat_id = ?";
                    $stmtDostep = mysqli_prepare($conn, $sqlSetNiedostepny);
                    if ($stmtDostep) {
                        mysqli_stmt_bind_param($stmtDostep, "i", $paczkomatId);
                        mysqli_stmt_execute($stmtDostep);
                        mysqli_stmt_close($stmtDostep);
                    }

                    $sqlUkryj = "UPDATE adresy_paczkomatów SET ukryty = 1 WHERE paczkomat_id = ?";
                    $stmtUkryj = mysqli_prepare($conn, $sqlUkryj);
                    if ($stmtUkryj) {
                        mysqli_stmt_bind_param($stmtUkryj, "i", $paczkomatId);
                        mysqli_stmt_execute($stmtUkryj);
                        mysqli_stmt_close($stmtUkryj);
                    }
                }
            } else {
                mysqli_stmt_close($stmtGet);
            }
        }
    }

    echo "OK";
} else {
    echo "Błąd zapisu przesyłki.";
}

mysqli_stmt_close($stmtInsert);
mysqli_close($conn);
