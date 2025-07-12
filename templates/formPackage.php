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

$sqlPrywatne = "
    SELECT a.adres_id, a.miasto, a.ulica, a.kod_pocztowy
    FROM użytkownicy u
    JOIN adresy_użytkowników au ON u.użytkownik_id = au.użytkownik_id
    JOIN adresy a ON au.adres_id = a.adres_id
    WHERE u.email = ? AND au.ukryty = 0
";
$stmt1 = mysqli_prepare($conn, $sqlPrywatne);
mysqli_stmt_bind_param($stmt1, "s", $email);
mysqli_stmt_execute($stmt1);
mysqli_stmt_bind_result($stmt1, $adres_id, $miasto, $ulica, $kod_pocztowy);

$prywatneOptions = "";
while (mysqli_stmt_fetch($stmt1)) {
    $full = "$ulica, $kod_pocztowy $miasto";
    $prywatneOptions .= "<option value=\"$adres_id\" data-type=\"prywatny\">$full</option>";
}
mysqli_stmt_close($stmt1);

$sqlPaczkomaty = "
    SELECT a.adres_id, a.miasto, a.ulica, a.kod_pocztowy, p.nazwa
    FROM paczkomaty p
    JOIN adresy_paczkomatów ap ON p.paczkomat_id = ap.paczkomat_id
    JOIN adresy a ON ap.adres_id = a.adres_id
    WHERE p.dostępność = 'dostępny'
";
$result2 = mysqli_query($conn, $sqlPaczkomaty);

$paczkomatOptions = "";
while ($row = mysqli_fetch_assoc($result2)) {
    $full = $row['nazwa'] . " – " . $row['ulica'] . ", " . $row['kod_pocztowy'] . " " . $row['miasto'];
    $paczkomatOptions .= "<option value=\"" . $row['adres_id'] . "\" data-type=\"paczkomat\">$full</option>";
}
mysqli_close($conn);
?>

<div class="package-form-container">
    <h2>Nadaj paczkę</h2>
    <form id="packageForm">

      <label for="address">Adres dostawy</label>
      <select id="address" name="address" required>
        <option value="">-- wybierz adres --</option>
        <optgroup label="Twoje adresy prywatne">
          <?= $prywatneOptions ?>
        </optgroup>
        <optgroup label="Paczkomaty">
          <?= $paczkomatOptions ?>
        </optgroup>
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

      <input type="hidden" name="typ_przesyłki" id="typ_przesyłki" value="">

      <div style="margin: 15px 0;">
        <label class="terms-label">
        <input type="checkbox" id="terms" name="terms" required>
        Akceptuję regulamin
         </label>
        <a href="/">REGULAMIN</a>
      </div>

      <div class="package-buttons">
        <button type="submit" class="send-btn">Nadaj paczkę</button>
        <button type="reset" class="cancel-send-btn" onclick="window.location.href='/'">Anuluj</button>
      </div>
    </form>
</div>

<script>
  document.getElementById('address').addEventListener('change', function () {
  console.log('Change event:', this.options[this.selectedIndex].dataset.type);
});
</script>
