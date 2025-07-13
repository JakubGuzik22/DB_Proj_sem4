<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    $polaczenie = new mysqli("localhost", "root", "", "firmakurierska");
    if ($polaczenie->connect_error) {
        echo "Błąd połączenia z bazą danych.";
        exit;
    }

    $stmt = $polaczenie->prepare("SELECT użytkownik_id FROM użytkownicy WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "Nie znaleziono użytkownika z podanym adresem email.";
        $stmt->close();
        $polaczenie->close();
        exit;
    }
    $stmt->close();

    function generujHaslo($dlugosc = 10) {
        $znaki = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($znaki), 0, $dlugosc);
    }

    $noweHaslo = generujHaslo();
    $noweHasloHash = password_hash($noweHaslo, PASSWORD_DEFAULT);

    $stmt = $polaczenie->prepare("UPDATE użytkownicy SET haslo_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $noweHasloHash, $email);

    if (!$stmt->execute()) {
        echo "Błąd przy aktualizacji hasła.";
        $stmt->close();
        $polaczenie->close();
        exit;
    }
    $stmt->close();
    $polaczenie->close();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'twoj.email@gmail.com';
        $mail->Password = 'twojehasloaplikacji';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        $mail->setFrom('noreply@firma-kurierska.pl', 'Firma kurierska');
        $mail->addAddress($email);

        $mail->Subject = 'Nowe hasło do konta';
        $mail->Body = "Witaj,\n\nTwoje nowe hasło to: $noweHaslo\n\nZaloguj się i zmień je jak najszybciej.";
        $mail->CharSet = 'UTF-8';

        $mail->send();
        echo "OK";
    } catch (Exception $e) {
        echo "Nie udało się wysłać e-maila.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
