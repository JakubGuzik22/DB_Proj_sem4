<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
            http_response_code(401);
            echo("Sesja wygasła. Zaloguj się ponownie.");
            exit;
        }

        $loggedInUserEmail = trim($_SESSION['email']);
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = mysqli_connect("localhost", "root", "", "firmakurierska");

        if ($conn->connect_error) {
            http_response_code(500);
            echo("Błąd połączenia z bazą danych.");
            exit;
        }

        $updateFields = [];
        $bindTypes = '';
        $bindValues = [];

        if (isset($_POST['login']) && trim($_POST['login']) !== '') {
            $updateFields[] = "`login` = ?";
            $bindTypes .= 's';
            $bindValues[] = trim($_POST['login']);
        }

        if (isset($_POST['firstName']) && trim($_POST['firstName']) !== '') {
            $updateFields[] = "`imie` = ?";
            $bindTypes .= 's';
            $bindValues[] = trim($_POST['firstName']);
        }

        if (isset($_POST['lastName']) && trim($_POST['lastName']) !== '') {
            $updateFields[] = "`nazwisko` = ?";
            $bindTypes .= 's';
            $bindValues[] = trim($_POST['lastName']);
        }

        if (isset($_POST['phone']) && trim($_POST['phone']) !== '') {
            $trimmedPhone = trim($_POST['phone']);
            // if (preg_match('/^[0-9]{9}$/', $trimmedPhone)) {
                $updateFields[] = "`nr_telefonu` = ?";
                $bindTypes .= 's';
                $bindValues[] = $trimmedPhone;
            // } else {
            //     http_response_code(400);
            //     echo("Nieprawidłowy format numeru telefonu. Oczekiwano 9 cyfr.");
            //     $conn->close();
            //     exit;
            // }
        }

        if (empty($updateFields)) {
            http_response_code(200);
            echo "Brak danych do aktualizacji lub podane dane są puste.";
            $conn->close();
            exit;
        }

        $sql = "UPDATE `użytkownicy` SET " . implode(", ", $updateFields) . " WHERE `email` = ?";
        $bindTypes .= 's';
        $bindValues[] = $loggedInUserEmail;

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            http_response_code(500);
            echo("Błąd zapytania.");
            $conn->close();
            exit;
        }

        $params = array_merge([$bindTypes], $bindValues);
        $refs = [];
        foreach($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);

        if ($stmt->execute()) {
            echo "OK";
        } else {
            http_response_code(500);
            echo("Błąd wykonania zapytania: ");
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(405);
        echo("Nieprawidłowa metoda żądania.");
    }