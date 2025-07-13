<?php
session_start();
$isLoggedIn = isset($_SESSION['email']);

if (!$isLoggedIn) {
    echo "Zaloguj się, aby dodać/edytować paczkomat.";
    exit;
}

$paczkomatId = isset($_GET['paczkomat_id']) ? $_GET['paczkomat_id'] : null;
$nazwa = $maks_pojemnosc = $aktualna_pojemnosc = $dostepnosc = '';
$selectedAdresId = null;

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Błąd połączenia z bazą";
    exit;
}
mysqli_set_charset($conn, "utf8");

if ($paczkomatId) {
    $sql = "SELECT nazwa, maksymalna_pojemność, aktualna_pojemność, dostępność 
            FROM paczkomaty WHERE paczkomat_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $paczkomatId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nazwa, $maks_pojemnosc, $aktualna_pojemnosc, $dostepnosc);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $sql2 = "SELECT adres_id FROM adresy_paczkomatów WHERE paczkomat_id = ? LIMIT 1";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $paczkomatId);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_bind_result($stmt2, $selectedAdresId);
    mysqli_stmt_fetch($stmt2);
    mysqli_stmt_close($stmt2);
}

$adresy = [];
$result = mysqli_query($conn, "SELECT adres_id, miasto, ulica, kod_pocztowy FROM adresy ORDER BY miasto, ulica");
while ($row = mysqli_fetch_assoc($result)) {
    $adresy[] = $row;
}
mysqli_close($conn);
?>

<div class="change-locker-container">
    <form method="post" id="changeLockerForm">
        <label for="nazwa">Nazwa paczkomatu</label>
        <input type="text" id="nazwa" name="nazwa" value="<?= htmlspecialchars($nazwa) ?>" required>

        <label for="maksymalna_pojemnosc">Maksymalna pojemność</label>
        <input type="number" id="maksymalna_pojemnosc" name="maksymalna_pojemnosc" min="1" value="<?= htmlspecialchars($maks_pojemnosc) ?>" required>

        <label for="adres_id">Adres paczkomatu</label>
        <select id="adres_id" name="adres_id" required>
            <option value="">-- wybierz adres --</option>
            <?php foreach ($adresy as $adres): ?>
                <option value="<?= $adres['adres_id'] ?>" <?= ($selectedAdresId == $adres['adres_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars("{$adres['miasto']}, {$adres['ulica']}, {$adres['kod_pocztowy']}") ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <div class="buttons">
            <button type="button" class="cancel-btn" onclick="event.preventDefault(); loadForm('packageLockerManagement');">Anuluj</button>
            <button type="submit" class="save-btn">Zapisz paczkomat</button>
        </div>
    </form>
</div>
