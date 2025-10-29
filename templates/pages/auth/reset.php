<h2>Set a new password</h2>

<form method="post" action="/password/reset">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

  <label>New password</label>
  <input type="password" name="password" required>

  <button>Update Password</button>
</form>

<p class="muted" style="margin-top:10px;">
  After resetting, <a href="/login">log in here</a>.
</p>
