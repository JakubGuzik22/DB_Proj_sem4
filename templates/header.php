<?php
session_start();
$isLoggedIn = isset($_SESSION['login']);
?>

<header class="header">
    <div class="logo">
        <img src="../resources/logo.png" alt="Logo firmy">
    </div>
    <div class="header-right">
        <nav class="nav">
            <a href="/">Nadaj paczkę</a>
            <a href="/">Moje paczki</a>
        </nav>
    </div>
    <div class="user-box">
        <?php if ($isLoggedIn): ?>
            <button>Zalogowano jako: <?= htmlspecialchars($_SESSION['login']) ?></button>
        <?php else: ?>
            <button onclick="event.preventDefault(); loadForm('form_login')">Zaloguj się</button>
        <?php endif; ?>
    </div>
</header>