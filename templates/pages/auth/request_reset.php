<h2>Reset your password</h2>

<form method="post" action="/password/forgot">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

  <label>Email address</label>
  <input type="email" name="email" placeholder="you@example.com" required>

  <button>Send reset link</button>
</form>

<p class="muted" style="margin-top:10px;">
  If your email exists, you’ll receive a password reset link.
</p>
