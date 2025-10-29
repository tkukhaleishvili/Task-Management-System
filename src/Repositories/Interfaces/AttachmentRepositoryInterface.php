<?php 
public function add(int $taskId, string $filename, string $storedPath, string $mime, int $size): int;
public function listByTask(int $taskId): array;

 ?>