<div class="register-container">
    <form id="registerForm">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="firstName">Imię</label>
        <input type="text" id="firstName" name="firstName" required>

        <label for="lastName">Nazwisko</label>
        <input type="text" id="lastName" name="lastName" required>

        <label for="phone">Nr. Telefonu</label>
        <input type="tel" id="phone" name="phone" placeholder="000000000" required pattern="[0-9]{3}[0-9]{3}[0-9]{3}">

        <label for="password">Hasło</label>
        <input type="password" id="password" name="password" required>

        <div class="buttons">
            <button type="reset" class="cancel-btn" onclick="event.preventDefault(); loadForm('form_login')">Anuluj</button>
            <button type="submit" class="register-btn">Zarejestruj się</button>
        </div>
    </form>
</div>