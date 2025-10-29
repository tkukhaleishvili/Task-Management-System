<h1>Edit Task #<?= htmlspecialchars($task->id) ?></h1>
<?php if (!empty($error)): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="/tasks/update">
  <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
  <label>Title <input type="text" name="title" value="<?= htmlspecialchars($task->title) ?>" required></label><br>
  <label>Description <textarea name="description"><?= htmlspecialchars($task->description) ?></textarea></label><br>
  <label>Category ID <input type="number" name="category_id" value="<?= htmlspecialchars($task->category_id) ?>"></label><br>
  <label>Assigned To (User ID) <input type="number" name="assigned_to" value="<?= htmlspecialchars($task->assigned_to) ?>"></label><br>
  <label>Priority
    <select name="priority">
      <?php foreach (['Low','Medium','High','Critical'] as $p): ?>
        <option <?= $task->priority===$p?'selected':'' ?> value="<?= $p ?>"><?= $p ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>
  <label>Status
    <select name="status">
      <?php foreach (['Pending','In Progress','Completed'] as $s): ?>
        <option <?= $task->status===$s?'selected':'' ?> value="<?= $s ?>"><?= $s ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>
  <label>Due Date <input type="date" name="due_date" value="<?= htmlspecialchars($task->due_date) ?>"></label><br>
  <button type="submit">Save</button>
</form>

<form method="POST" action="/tasks/delete" onsubmit="return confirm('Delete this task?')">
  <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
  <button type="submit" style="background:#c00;color:#fff">Delete</button>
</form>
