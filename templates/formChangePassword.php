<form id="changePasswordForm">
    <label for="currentPassword">Aktualne hasło:</label><br>
    <input type="password" id="currentPassword" name="currentPassword" required><br><br>

    <label for="newPassword">Nowe hasło:</label><br>
    <input type="password" id="newPassword" name="newPassword" required minlength="6"><br><br>

    <label for="confirmPassword">Potwierdź nowe hasło:</label><br>
    <input type="password" id="confirmPassword" name="confirmPassword" required minlength="6"><br><br>

    <div class="buttons vertical">
        <button type="submit" class="save-btn">Zmień hasło</button>
        <button type="button" class="cancel-btn">Anuluj</button>
    </div>
</form>
