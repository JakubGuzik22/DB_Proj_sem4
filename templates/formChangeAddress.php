<?php
    session_start();
    $isLoggedIn = isset($_SESSION['email']);

    if (!$isLoggedIn) {
        echo "Zaloguj się, aby dodać/edytować adres.";
        exit;
    }
    $adresId = isset($_GET['adres_id']) ? $_GET['adres_id'] : null;
    $miasto = $ulica = $kod_pocztowy = '';

    if ($adresId) {
        $conn = mysqli_connect("localhost", "root", "", "firmakurierska");
        if (!$conn) {
            http_response_code(500);
            echo "Błąd połączenia z bazą";
            exit;
        }
        mysqli_set_charset($conn, "utf8");
        $sql = "SELECT miasto, ulica, kod_pocztowy FROM adresy WHERE adres_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $adresId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $miasto, $ulica, $kod_pocztowy);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
?>

<div class="change-address-container">
    <form method="post" id="changeAddressForm">
        <label for="miasto">Miasto</label>
        <input type="text" id="miasto" name="miasto" placeholder="<?= htmlspecialchars($miasto) ?>">

        <label for="ulica">Ulica</label>
        <input type="text" id="ulica" name="ulica" placeholder="<?= htmlspecialchars($ulica) ?>">

        <label for="kod_pocztowy">Kod pocztowy</label>
        <input type="text" id="kod_pocztowy" name="kod_pocztowy" maxlength="6" placeholder="<?= $kod_pocztowy ? htmlspecialchars($kod_pocztowy) : '00-000' ?>" pattern="^\d{2}-\d{3}$">
        
        <div class="buttons">
            <button type="button" class="cancel-btn" onclick="event.preventDefault(); loadForm('addresses');">Anuluj</button>
            <button type="submit" class="save-btn">Zapisz adres</button>
        </div>
    </form>
</div>