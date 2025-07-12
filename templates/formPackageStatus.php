<?php
$packageId = $_GET['paczka_id'] ?? null;
$currentStatus = 'oczekuje';
$allowedStatuses = [];

if ($packageId) {
    $conn = mysqli_connect("localhost", "root", "", "firmakurierska");
    if ($conn) {
        $stmt = mysqli_prepare($conn, "SELECT aktualny_status FROM przesyłki WHERE przesyłka_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $packageId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $status);
        if (mysqli_stmt_fetch($stmt)) {
            $currentStatus = $status;
        }
        mysqli_stmt_close($stmt);

        $stmtHist = mysqli_prepare($conn, "SELECT COUNT(*) FROM historia_zamówień WHERE przesyłka_id = ? AND status IN ('oczekuje','w_drodze')");
        mysqli_stmt_bind_param($stmtHist, "i", $packageId);
        mysqli_stmt_execute($stmtHist);
        mysqli_stmt_bind_result($stmtHist, $count);
        mysqli_stmt_fetch($stmtHist);
        mysqli_stmt_close($stmtHist);

        mysqli_close($conn);

        if ($currentStatus === 'oczekuje') {
            if ($count === 0) {
                $allowedStatuses = ['oczekuje', 'w_drodze', 'dostarczona'];
            } else {
                $allowedStatuses = ['w_drodze', 'dostarczona'];
            }
        } elseif ($currentStatus === 'w_drodze') {
            $allowedStatuses = ['dostarczona'];
        } elseif ($currentStatus === 'dostarczona') {
            $allowedStatuses = [];
        } else {
            $allowedStatuses = ['oczekuje', 'w_drodze', 'dostarczona'];
        }
    }
}
?>

<div class="package-status-form-container">
    <h2>Aktualizuj status paczki</h2>
    <form id="packageStatusForm">
        <input type="hidden" name="paczka_id" value="<?= htmlspecialchars($packageId) ?>">
        
        <label for="status">Status paczki</label>
        <select name="status" id="status" required>
            <?php foreach ($allowedStatuses as $statusOption): ?>
                <option value="<?= $statusOption ?>" <?= $currentStatus === $statusOption ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_', ' ', $statusOption)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="package-status-buttons" style="margin-top: 15px;">
            <button type="submit" class="save-btn"
                <?= empty($allowedStatuses) ? 'disabled title="Brak możliwości zmiany statusu"' : '' ?>
            >Zapisz</button>
            <button type="button" class="cancel-btn" onclick="loadView('packageManagement')">Anuluj</button>
        </div>
    </form>
</div>
