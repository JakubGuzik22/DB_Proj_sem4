<?php
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
?>

<form id="changeRoleForm">
    <input type="hidden" name="user_id" value="<?php echo $userId; ?>" />
    
    <label for="role">Wybierz rolę:</label>
    <select name="role" id="role" required>
        <option value="klient">Klient</option>
        <option value="pracownik">Pracownik</option>
        <option value="admin">Administrator</option>
    </select>
    <div class="buttons vertical">
        <button type="submit" class="save-btn">Zmień rolę</button>  
        <button type="button" class="cancel-btn" onclick="event.preventDefault(); loadView('userManagement');">Anuluj</button>
    </div>
</form>
