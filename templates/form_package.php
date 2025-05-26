<div class="package-form-container">
    <h2>Nadaj paczkę</h2>
    <form method="post" action="">
      <label for="name">Imię i nazwisko odbiorcy</label>
      <input type="text" id="name" name="name" placeholder="" required>

      <label for="address">Adres</label>
      <select id="address" name="address" required>
        <option value=""></option>
      </select>

      <div style="margin: 15px 0;">
        <input type="checkbox" id="terms" name="terms" required checked>
        <label for="terms" style="display: inline; color: #333;">Akceptuję regulamin</label><br>
        <a href="#">REGULAMIN</a>
      </div>

      <button type="submit">Nadaj paczkę</button>
    </form>
</div> 
