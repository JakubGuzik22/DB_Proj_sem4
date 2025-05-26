<div class="login-container">
    <form method="post" action="">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="" required>
        <label for="password">Hasło</label>
        <input type="password" id="password" name="password" placeholder="" required>
        <button type="submit">Zaloguj</button>

        <div class="links">
            <a href="/" onclick="event.preventDefault(); loadForm('form_passreset')">Zapomniałeś/aś hasła?</a>
            <a href="/" onclick="event.preventDefault(); loadForm('form_register')">Nie masz konta? Zarejestruj się</a>
        </div>
    </form>
</div>