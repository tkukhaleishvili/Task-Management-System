<h2>Register</h2>

<form method="post" action="/register">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

  <div class="row">
    <div>
      <label>Username</label>
      <input name="username" required>
    </div>
    <div>
      <label>Email</label>
      <input name="email" type="email" required>
    </div>
  </div>

  <div class="row">
    <div>
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
  </div>

  <button>Create Account</button>
</form>

<p class="muted" style="margin-top:12px;">
  Already have an account? <a href="/login">Log in</a>
</p>
