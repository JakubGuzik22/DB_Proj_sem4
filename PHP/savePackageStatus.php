<?php
$packageId = $_POST['paczka_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$packageId || !$status) {
    echo "Brak wymaganych danych";
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "firmakurierska");
if (!$conn) {
    echo "Nie udało się połączyć z bazą danych";
    exit;
}

$allowedStatuses = ['oczekuje', 'w_drodze', 'dostarczona'];
if (!in_array($status, $allowedStatuses)) {
    echo "Niepoprawny status";
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE przesyłki SET aktualny_status = ? WHERE przesyłka_id = ?");
if (!$stmt) {
    echo "Nie udało się przygotować zapytania";
    exit;
}
mysqli_stmt_bind_param($stmt, "si", $status, $packageId);

if (mysqli_stmt_execute($stmt)) {
    $stmtHist = mysqli_prepare($conn, "INSERT INTO historia_zamówień (przesyłka_id, status, data) VALUES (?, ?, NOW())");
    if ($stmtHist) {
        mysqli_stmt_bind_param($stmtHist, "is", $packageId, $status);
        mysqli_stmt_execute($stmtHist);
        mysqli_stmt_close($stmtHist);
    }
    echo "OK";
} else {
    echo "Nie udało się zaktualizować statusu";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
