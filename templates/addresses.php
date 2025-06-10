<?php
session_start();

$conn = new mysqli("localhost", "root", "", "firmakurierska");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

if (!isset($_SESSION['login'])) {
    die("Musisz być zalogowany, aby przeglądać i edytować adresy.");
}

$login = $_SESSION['login'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['adres_id'])) {
        die("Brak wymaganego identyfikatora adresu.");
    }

    $adres_id = (int)$_POST['adres_id'];
    $kod = $_POST['kod_pocztowy'];
    $miasto = $_POST['miasto'];
    $ulica = $_POST['ulica'];

    if (empty($kod) || empty($miasto) || empty($ulica)) {
        die("Wszystkie pola adresu muszą być wypełnione.");
    }

    $sqlUpdate = "
        UPDATE `adresy` a
        JOIN `adresy_użytkowników` au ON a.`adres_id` = au.`adres_id`
        JOIN `użytkownicy` u ON au.`użytkownik_id` = u.`użytkownik_id`
        SET a.`kod_pocztowy` = ?, a.`miasto` = ?, a.`ulica` = ?
        WHERE u.`login` = ? AND a.`adres_id` = ?
    ";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    if (!$stmtUpdate) {
        die("Błąd przygotowania zapytania: " . $conn->error);
    }

    $stmtUpdate->bind_param("ssssi", $kod, $miasto, $ulica, $login, $adres_id);

    if ($stmtUpdate->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
        exit;
    } else {
        die("Błąd wykonania zapytania: " . $stmtUpdate->error);
    }

    $stmtUpdate->close(); 
}

$sql = "
    SELECT a.`adres_id`, a.`kod_pocztowy`, a.`miasto`, a.`ulica`
    FROM `adresy` a
    JOIN `adresy_użytkowników` au ON a.`adres_id` = au.`adres_id`
    JOIN `użytkownicy` u ON au.`użytkownik_id` = u.`użytkownik_id`
    WHERE u.`login` = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Błąd przygotowania zapytania do pobierania adresów: " . $conn->error);
}
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

$adresy = [];
while ($row = $result->fetch_assoc()) {
    $adresy[] = $row;
}
$stmt->close(); 
$conn->close(); 
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresy użytkownika</title>

    <style>
        body {
            color:white;
            font-family: sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .address-item {
            border: 1px solid #eee;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }
        .edit-button {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            align-self: flex-end;
            margin-top: 5px;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close-button {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        .form-actions button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .submit-button {
            background-color: #28a745;
            color: white;
        }
        .cancel-button {
            background-color: #dc3545;
            color: white;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #c3e6cb;
        }
    </style>
    <script>
        function showEditForm(adresId, kodPocztowy, miasto, ulica) {
            console.log('showEditForm called with:', adresId, kodPocztowy, miasto, ulica);
            const editAdresId = document.getElementById('edit-adres-id');
            const editKodPocztowy = document.getElementById('edit-kod_pocztowy');
            const editMiasto = document.getElementById('edit-miasto');
            const editUlica = document.getElementById('edit-ulica');
            const editAddressModal = document.getElementById('edit-address-modal');
        }

        function closeEditForm() {
            console.log('closeEditForm called');
            const modal = document.getElementById('edit-address-modal');
            if (modal) {
                modal.style.display = 'none';
            } else {
                console.error("Element with ID 'edit-address-modal' not found for closing.");
            }
        }

        window.onclick = function(event) {
            const modal = document.getElementById('edit-address-modal');
            if (modal && event.target == modal) {
                closeEditForm();
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Adresy przypisane do Twojego konta</h1>

    <?php
    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        echo '<p class="success-message">Adres został pomyślnie zaktualizowany!</p>';
    }
    ?>

    <?php if (empty($adresy)): ?>
        <p>Brak adresów przypisanych do Twojego konta.</p>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($adresy as $i => $adres): ?>
                <div class="address-item">
                    <p class="address-text">
                        <strong>Adres <?= $i + 1 ?>:</strong><br>
                        [<?= htmlspecialchars($adres['kod_pocztowy']) ?>]
                        [<?= htmlspecialchars($adres['miasto']) ?>]
                        [<?= htmlspecialchars($adres['ulica']) ?>]
                    </p>
                    <button class="edit-button"
                            onclick="showEditForm(
                                        <?= $adres['adres_id'] ?>,
                                        '<?= htmlspecialchars($adres['kod_pocztowy'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($adres['miasto'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($adres['ulica'], ENT_QUOTES) ?>'
                                    )">
                        Edytuj
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="edit-address-modal" class="modal-overlay">
    <div class="modal-content">

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <input type="hidden" name="adres_id" id="edit-adres-id">

            <div class="form-group">
                <label for="edit-kod_pocztowy">Kod Pocztowy:</label>
                <input type="text" id="edit-kod_pocztowy" name="kod_pocztowy" required>
            </div>

            <div class="form-group">
                <label for="edit-miasto">Miasto:</label>
                <input type="text" id="edit-miasto" name="miasto" required>
            </div>

            <div class="form-group">
                <label for="edit-ulica">Ulica:</label>
                <input type="text" id="edit-ulica" name="ulica" required>
            </div>

            <div class="form-actions">
                <button type="button" onclick="closeEditForm()" class="cancel-button">Anuluj</button>
                <button type="submit" class="submit-button">Zapisz Zmiany</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
