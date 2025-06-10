<div class="change-settings-container">
    <form method="post" id="changeSettingsForm">
      <label for="login">Login</label>
      <input type="text" id="login" name="login">

      <label for="firstName">Imię</label>
      <input type="text" id="firstName" name="firstName">

      <label for="lastName">Nazwisko</label>
      <input type="text" id="lastName" name="lastName">

      <label for="phone">Nr. Telefonu</label>
      <input type="tel" id="phone" name="phone" placeholder="000000000" pattern="[0-9]{3}[0-9]{3}[0-9]{3}">

      <div class="buttons">
        <button type="button" class="cancel-btn">Anuluj</button>
        <button type="submit" class="save-btn">Zmień dane osobiste</button>
      </div>
    </form>
</div>
