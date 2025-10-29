<?php 

public function add(int $taskId, int $userId, string $comment): int;
public function listByTask(int $taskId): array;

 ?>