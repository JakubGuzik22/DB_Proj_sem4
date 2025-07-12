<?php
session_start();
mysqli_report(MYSQLI_REPORT_OFF);
@$conn = mysqli_connect("localhost", "root", "", "firmakurierska");

if (!$conn) {
    http_response_code(500);
    echo "Błąd połączenia z bazą";
    exit;
}

mysqli_set_charset($conn, "utf8");

if (!isset($_SESSION['email'])) {
    echo "Zaloguj się, aby zobaczyć adresy.";
    exit;
}

$email = trim($_SESSION['email']);

$sql = "
    SELECT DISTINCT
        a.adres_id,
        a.miasto,
        a.ulica,
        a.kod_pocztowy
    FROM użytkownicy u
    JOIN adresy_użytkowników au ON u.użytkownik_id = au.użytkownik_id
    JOIN adresy a ON au.adres_id = a.adres_id
    WHERE u.email = ? AND au.ukryty = 0
";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Błąd zapytania.";
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
mysqli_stmt_bind_result($stmt, $adres_id, $miasto, $ulica, $kod_pocztowy);

$addressOptions = "";

while (mysqli_stmt_fetch($stmt)) {
    $fullAddress = $ulica . ", " . $kod_pocztowy . " " . $miasto;
    $addressOptions .= "<option value=\"" . $adres_id . "\">" . $fullAddress . "</option>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<div class="package-form-container">
    <h2>Nadaj paczkę</h2>
    <form id="packageForm">
      <!--<label for="name">Imię i nazwisko odbiorcy</label>-->
      <!-- <input type="text" id="name" name="name" required> -->

      <label for="address">Adres</label>
      <select id="address" name="address" required>
        <option value="">-- wybierz adres --</option>
        <?= $addressOptions ?>
      </select>

      <label for="size">Rozmiar paczki</label>
      <select id="size" name="size" required>
        <option value="">-- wybierz rozmiar --</option>
        <option value="mała">Mała</option>
        <option value="średnia">Średnia</option>
        <option value="duża">Duża</option>
      </select>

      <label for="weight">Waga (kg)</label>
      <input type="number" id="weight" name="weight" min="0.1" step="0.1" required>

      <label for="type">Typ przesyłki</label>
      <select id="type" name="type" required>
        <option value="">-- wybierz typ --</option>
        <option value="ekspres">Ekspres</option>
        <option value="standard">Standard</option>
      </select>

      <div style="margin: 15px 0;">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms" style="color: #333;">Akceptuję regulamin</label><br>
        <a href="/">REGULAMIN</a>
      </div>

      <div class="package-buttons">
        <button type="submit" class="send-btn">Nadaj paczkę</button>
        <button type="reset" class="cancel-send-btn" onclick="window.location.href='/'">Anuluj</button>
      </div>
    </form>
</div> 
