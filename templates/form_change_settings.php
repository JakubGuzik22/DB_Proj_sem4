<div class="change-settings-container">
    <form method="post" id="changeSettingsForm">
      <label for="firstName">Imię</label>
      <input type="text" id="firstName" name="firstName" placeholder="Value" required>

      <label for="lastName">Nazwisko</label>
      <input type="text" id="lastName" name="lastName" placeholder="Value" required>

      <label for="phone">Nr. Telefonu</label>
      <input type="tel" id="phone" name="phone" placeholder="000000000" required pattern="[0-9]{3}[0-9]{3}[0-9]{3}">

      <div class="buttons">
        <button type="button" class="cancel-btn">Anuluj</button>
        <button type="submit" class="save-btn">Zmień dane osobiste</button>
      </div>
    </form>
</div>
