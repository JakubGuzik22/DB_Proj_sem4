<?php
session_start();
$isLoggedIn = isset($_SESSION['login']);
?>

<header class="header">
    <div class="logo">
        <img src="../resources/logo.png" alt="Logo firmy">
    </div>
    <div class="header-right">
        <?php if ($isLoggedIn): ?>
            <nav class="nav">
                <a href="/">Nadaj paczkę</a>
                <a href="/">Moje paczki</a>
            </nav>
        <?php endif; ?>
        <div class="user-box">
            <?php if ($isLoggedIn): ?>
                <form method="post">    
                    <select name="user_action">
                        <option disabled selected>Zalogowano jako: <?= htmlspecialchars($_SESSION['login']) ?></option>
                        <option value="addresses">Moje Adresy</option>
                        <option value="settings">Ustawienia Konta</option>
                        <option value="logout">Wyloguj</option>
                    </select>
                </form>
            <?php else: ?>
                <button onclick="event.preventDefault(); loadForm('form_login')">Zaloguj się</button>
            <?php endif; ?>
        </div>
    </div>
</header>