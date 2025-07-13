<div class="register-container">
  <form id="passResetForm">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>
        
    <div class="buttons">
      <button type="reset" class="cancel-btn" onclick="event.preventDefault(); loadForm('formLogin')">Anuluj</button>
      <button type="submit" class="register-btn">Zresetuj hasło</button>
    </div>
  </form>
</div>