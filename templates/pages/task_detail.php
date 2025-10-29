<form method="POST" action="/tasks/status">
  <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
  <select name="status">
    <?php foreach (['Pending','In Progress','Completed'] as $s): ?>
      <option <?= $task->status===$s?'selected':'' ?> value="<?= $s ?>"><?= $s ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Update Status</button>
</form>

<h3 id="attachments">Attachments</h3>
<form method="POST" action="/tasks/attach" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
  <input type="file" name="file" required>
  <button type="submit">Upload</button>
</form>
<ul>
  <?php foreach (($attachments ?? []) as $a): ?>
    <li><?= htmlspecialchars($a['filename']) ?> (<?= (int)$a['size_bytes'] ?> bytes)</li>
  <?php endforeach; ?>
</ul>

<h3 id="comments">Comments</h3>
<form method="POST" action="/tasks/comment">
  <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
  <textarea name="comment" required></textarea>
  <button type="submit">Add Comment</button>
</form>
<ul>
  <?php foreach (($comments ?? []) as $c): ?>
    <li><strong><?= htmlspecialchars($c['username']) ?>:</strong> <?= nl2br(htmlspecialchars($c['comment'])) ?> <em>(<?= htmlspecialchars($c['created_at']) ?>)</em></li>
  <?php endforeach; ?>
</ul>
