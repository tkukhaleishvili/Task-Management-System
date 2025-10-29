<?php 

public function add(...) : int {
  $stmt=$this->pdo->prepare("INSERT INTO task_attachments (task_id,filename,stored_path,mime_type,size_bytes) VALUES (:t,:f,:p,:m,:s)");
  $stmt->execute([':t'=>$taskId,':f'=>$filename,':p'=>$storedPath,':m'=>$mime,':s'=>$size]);
  return (int)$this->pdo->lastInsertId();
}
public function listByTask(int $taskId): array {
  $stmt=$this->pdo->prepare("SELECT * FROM task_attachments WHERE task_id=:t ORDER BY id DESC");
  $stmt->execute([':t'=>$taskId]);
  return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

 ?>