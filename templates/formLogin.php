<div class="login-container">
    <form id="loginForm">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="" required>
        <label for="password">Hasło</label>
        <input type="password" id="password" name="password" placeholder="" required>
        <button type="submit">Zaloguj</button>

        <div class="links">
            <a href="/" onclick="event.preventDefault(); loadForm('formPassreset')">Zapomniałeś/aś hasła?</a>
            <a href="/" onclick="event.preventDefault(); loadForm('formRegister')">Nie masz konta? Zarejestruj się</a>
        </div>
    </form>
</div>