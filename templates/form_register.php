<div class="register-container">
    <form method="POST" action="/PHP/register.php">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="first-name">Imię</label>
        <input type="text" id="first-name" name="first-name" required>

        <label for="last-name">Nazwisko</label>
        <input type="text" id="last-name" name="last-name" required>

        <label for="phone">Nr. Telefonu</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="password">Hasło</label>
        <input type="password" id="password" name="password" required>

        <div class="buttons">
            <input type="reset" class="cancel-btn" onclick="event.preventDefault(); loadForm('form_login')" value="Anuluj">
            <input type="submit" class="register-btn" value="Zarejestruj się">
        </div>
    </form>
</div>