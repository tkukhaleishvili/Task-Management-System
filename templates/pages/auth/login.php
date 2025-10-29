<h1>Login</h1>

<?php if (!empty($lastUser)): ?>
  <p>Welcome back, <strong><?= htmlspecialchars($lastUser['username'] ?: $lastUser['email']) ?></strong></p>
<?php endif; ?>

<form method="POST" action="/login">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

  <?php if (empty($lastUser)): ?>
    <label>Email or Username:<br>
      <input type="text" name="login" required>
    </label><br><br>
  <?php else: ?>
    <input type="hidden" name="login" value="<?= htmlspecialchars($lastUser['email'] ?: $lastUser['username']) ?>">
  <?php endif; ?>

  <label>Password:<br>
    <input type="password" name="password" required>
  </label><br><br>

  <label><input type="checkbox" name="remember"> Stay signed in</label><br><br>

  <button type="submit">Login</button>
</form>
<p class="muted" style="margin-top:12px;">
  <a href="/password/forgot">Forgot your password?</a>
</p>