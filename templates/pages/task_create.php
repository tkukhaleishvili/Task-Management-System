<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Task</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <h1>Create New Task</h1>

  <?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form action="/tasks/store" method="POST" style="max-width:400px;">
    <label>Title:<br>
      <input type="text" name="title" required>
    </label><br><br>

    <label>Description:<br>
      <textarea name="description" rows="3"></textarea>
    </label><br><br>

    <label>Category ID:<br>
      <input type="number" name="category_id" min="1">
    </label><br><br>

    <label>Assigned To (User ID):<br>
      <input type="number" name="assigned_to" min="1">
    </label><br><br>

    <label>Created By (User ID):<br>
      <input type="number" name="created_by" min="1" required>
    </label><br><br>

    <label>Priority:<br>
      <select name="priority">
        <option value="Low">Low</option>
        <option value="Medium" selected>Medium</option>
        <option value="High">High</option>
        <option value="Critical">Critical</option>
      </select>
    </label><br><br>

    <label>Status:<br>
      <select name="status">
        <option value="Pending" selected>Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
      </select>
    </label><br><br>

    <label>Due Date:<br>
      <input type="date" name="due_date">
    </label><br><br>

    <button type="submit" style="padding:6px 12px;">Save Task</button>
  </form>

  <p><a href="/">Back to Dashboard</a></p>
</body>
</html>
