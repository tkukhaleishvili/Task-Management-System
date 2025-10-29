<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Dashboard</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>
    body {font-family: system-ui, sans-serif; margin: 40px; background: #f9f9fb; color: #222;}
    h1 {margin-bottom: 20px;}
    table {border-collapse: collapse; width: 100%;}
    th, td {padding: 10px; border: 1px solid #ccc; text-align: left;}
    th {background: #f0f0f0;}
    a.button {
      display:inline-block;margin:10px 0;padding:8px 12px;
      background:#007bff;color:#fff;text-decoration:none;border-radius:4px;
    }
    a.button:hover {background:#0056b3;}
    .flash {padding:10px;border-radius:8px;margin:10px 0;}
    .flash.success{background:#e7f8ed;color:#276738;}
    .flash.error{background:#fde8e8;color:#8a1f1f;}
    .top-bar {display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
    .status {
      padding:4px 8px;border-radius:6px;color:#fff;font-weight:600;font-size:0.85rem;
    }
    .status.Pending {background:#6c757d;}
    .status.In\ Progress {background:#0d6efd;}
    .status.Completed {background:#198754;}
    form.filter {display:flex;gap:10px;align-items:center;margin-bottom:15px;}
    form.filter select, form.filter input {padding:5px;}
    .actions a {margin-right:6px;}
  </style>
</head>
<body>
<p>Welcome, <strong><?= htmlspecialchars($user['username'] ?? 'User') ?></strong>!</p>
  <div class="top-bar">
    <h1>Task Dashboard</h1>

    <?php if (!empty($_SESSION['user_id'])): ?>
      <form method="post" action="/logout">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf'] ?? '') ?>">
        <button style="background:#dc3545;color:#fff;border:0;padding:8px 12px;border-radius:4px;cursor:pointer;">
          Logout
        </button>
      </form>
    <?php endif; ?>
  </div>

  <?php if (!empty($success)): ?>
    <div class="flash success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="flash error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <a href="/tasks/create" class="button">➕ Create Task</a>

  <form method="GET" action="/tasks" class="filter">
    <input type="text" name="q" placeholder="Search title or description"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    <select name="status">
      <option value="">All Statuses</option>
      <?php foreach (['Pending','In Progress','Completed'] as $s): ?>
        <option value="<?= $s ?>" <?= (($_GET['status'] ?? '')===$s)?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <select name="sort">
      <option value="">Sort by</option>
      <option value="due_asc"   <?= (($_GET['sort']??'')==='due_asc')?'selected':'' ?>>Due ↑</option>
      <option value="due_desc"  <?= (($_GET['sort']??'')==='due_desc')?'selected':'' ?>>Due ↓</option>
      <option value="prio"      <?= (($_GET['sort']??'')==='prio')?'selected':'' ?>>Priority</option>
      <option value="created_desc" <?= (($_GET['sort']??'')==='created_desc')?'selected':'' ?>>Newest</option>
    </select>
    <button type="submit">Apply</button>
  </form>

  <?php if (empty($tasks)): ?>
    <p>No tasks found.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Assigned To</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Due Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($tasks as $task): ?>
        <tr>
          <td><?= htmlspecialchars($task->id) ?></td>
          <td>
            <a href="/task?id=<?= $task->id ?>">
              <?= htmlspecialchars($task->title) ?>
            </a>
          </td>
          <td><?= htmlspecialchars($task->category_name ?? '—') ?></td>
          <td><?= htmlspecialchars($task->assigned_user ?? '—') ?></td>
          <td><?= htmlspecialchars($task->priority) ?></td>
          <td><span class="status <?= htmlspecialchars($task->status) ?>">
            <?= htmlspecialchars($task->status) ?>
          </span></td>
          <td><?= htmlspecialchars($task->due_date ?: '—') ?></td>
          <td class="actions">
            <a href="/tasks/edit?id=<?= $task->id ?>"> Edit</a>
            <form method="POST" action="/tasks/delete" style="display:inline"
                  onsubmit="return confirm('Delete this task?')">
              <input type="hidden" name="id" value="<?= $task->id ?>">
              <button type="submit" style="background:none;border:none;color:#c00;cursor:pointer;">
                 Delete
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</body>
</html>
