<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Managers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {font-family: system-ui, sans-serif; background:#f6f7fb; margin:0; color:#222;}
        header,main {max-width: 960px; margin: 0 auto; padding: 16px;}
        nav a {margin-right: 10px; text-decoration:none; color:#0366d6;}
        .card {background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:24px; margin-top:20px;}
        .flash {padding:10px;border-radius:8px;margin:8px 0;}
        .flash.success{background:#e7f8ed;color:#276738;}
        .flash.error{background:#fde8e8;color:#8a1f1f;}
        input,textarea,button,select{padding:10px;border:1px solid #ccc;border-radius:6px;width:100%;box-sizing:border-box;}
        button{background:#111;color:#fff;border:0;cursor:pointer;}
        button:hover{background:#333;}
        .row{display:flex;gap:12px;flex-wrap:wrap;}
        .row>div{flex:1 1 280px;}
        .muted{color:#666;}
    </style>
</head>
<body>

<header>
  <h2>Task Managers</h2>
  <nav>
    <a href="/">Home</a>
    <a href="/login">Login</a>
    <a href="/register">Register</a>
  </nav>
</header>

<main>
  <?php if (!empty($success)): ?><div class="flash success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="card">
    <?php include $viewFile; ?>
  </div>
</main>

</body>
</html>
