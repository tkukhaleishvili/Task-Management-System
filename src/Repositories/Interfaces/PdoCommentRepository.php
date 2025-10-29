<?php 

public function add(...) : int {
  $stmt=$this->pdo->prepare("INSERT INTO task_comments (task_id,user_id,comment) VALUES (:t,:u,:c)");
  $stmt->execute([':t'=>$taskId,':u'=>$userId,':c'=>$comment]);
  return (int)$this->pdo->lastInsertId();
}
public function listByTask(int $taskId): array {
  $stmt=$this->pdo->prepare("
     SELECT tc.*, u.username
     FROM task_comments tc
     JOIN users u ON u.id = tc.user_id
     WHERE tc.task_id = :t
     ORDER BY tc.id DESC
  ");
  $stmt->execute([':t'=>$taskId]);
  return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

 ?>