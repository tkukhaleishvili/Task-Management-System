<?php
namespace App\Services;

use App\Repositories\PdoTaskRepository;
use App\Models\Task;
use App\Services\AuthService;

class TaskService
{
    private PdoTaskRepository $repo;
    private ?AttachmentRepository $attachments = null; 
    private ?AuthService $auth; 

    public function __construct(PdoTaskRepository $repo, ?AuthService $auth = null)
    {
        $this->repo = $repo;
        $this->auth = $auth; 
    }
    public function createTask(array $data): Task
    {
        if (empty($data['title'])) {
            throw new InvalidArgumentException('Task title is required.');
        }
        if (empty($data['created_by'])) {
            throw new InvalidArgumentException('Task must have a creator.');
        }

        $allowedPriorities = ['Low', 'Medium', 'High', 'Critical'];
        $allowedStatuses   = ['Pending', 'In Progress', 'Completed'];

        if (isset($data['priority']) && !in_array($data['priority'], $allowedPriorities, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid priority '%s'. Allowed values: %s",
                    $data['priority'],
                    implode(', ', $allowedPriorities)
                )
            );
        }

        if (isset($data['status']) && !in_array($data['status'], $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid status '%s'. Allowed values: %s",
                    $data['status'],
                    implode(', ', $allowedStatuses)
                )
            );
        }

        $data['title'] = htmlspecialchars(trim($data['title']), ENT_QUOTES, 'UTF-8');
        if (isset($data['description'])) {
            $data['description'] = htmlspecialchars(trim($data['description']), ENT_QUOTES, 'UTF-8');
        }
        $task = $this->repo->create($data);
        return $task;
    }

    public function getAllTasks(): array
    {
        return $this->repo->findAll();
    }

    public function getTasksByUser(int $userId): array
    {
        return $this->repo->findByUser($userId);
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->repo->findById($id);
    }

    public function updateTask(int $id, array $data, int $actorId): bool {
        $task = $this->repo->findById($id);
        if (!$task) throw new \RuntimeException('Task not found');
        if (!$this->canEdit($task, $actorId)) throw new \RuntimeException('Forbidden');
        $oldAssigned = $task->assigned_to ?? null;
        $ok = $this->repo->update($id, $data);

        if ($ok && isset($data['assigned_to']) && (int)$data['assigned_to'] !== (int)$oldAssigned) {
            $this->notifyAssignment((int)$data['assigned_to'], $id);
        }
        return $ok;
    }

    public function deleteTask(int $id, int $actorId): bool {
        $task = $this->repo->findById($id)
        ;
        if (!$task) return false;
        if (!$this->canEdit($task, $actorId)) throw new \RuntimeException('Forbidden');
        return $this->repo->softDelete($id);
    }

    public function changeStatus(int $id, string $status, int $actorId): bool {
        $allowed = ['Pending','In Progress','Completed'];
        if (!in_array($status, $allowed, true)) throw new \InvalidArgumentException('Bad status');
        $task = $this->repo->findById($id);
        if (!$task) return false;
        if (!$this->canEdit($task, $actorId)) throw new \RuntimeException('Forbidden');
        return $this->repo->changeStatus($id, $status);
    }

    public function listTasksFiltered(array $filters): array
    {
        return $this->repo->findByFilters($filters);
    }

    public function attachFile(int $taskId, array $file, int $actorId): int {
        $task = $this->repo->findById($taskId);
        if (!$task) throw new \RuntimeException('Task not found');
        if (!$this->canEdit($task, $actorId)) throw new \RuntimeException('Forbidden');

        if ($file['error'] !== UPLOAD_ERR_OK) throw new \RuntimeException('Upload error');
        $name = basename($file['name']);
        $mime = mime_content_type($file['tmp_name']) ?: 'application/octet-stream';
        $size = (int)$file['size'];

        $targetDir = __DIR__ . '/../../storage/attachments';
        if (!is_dir($targetDir)) mkdir($targetDir, 0775, true);
        $stored = $targetDir . '/' . uniqid('att_', true) . '_' . preg_replace('/[^A-Za-z0-9._-]/','_',$name);
        move_uploaded_file($file['tmp_name'], $stored);

        return $this->attachments->add($taskId, $name, $stored, $mime, $size);
    }

    public function addComment(int $taskId, int $userId, string $comment): int {
        $task = $this->repo->findById($taskId);
        if (!$task) throw new \RuntimeException('Task not found');
        if (trim($comment) === '') throw new \InvalidArgumentException('Empty comment');
        return $this->comments->add($taskId, $userId, $comment);
    }

    private function canEdit($task, int $actorId): bool {
        if (!$this->auth) return false; 
        $actor = $this->auth->getUserById($actorId);
        $role = $actor['role'] ?? 'User';
        return ($task->created_by == $actorId)
            || ($task->assigned_to == $actorId)
            || in_array($role, ['Admin', 'Manager'], true);
    }

    private function notifyAssignment(int $assigneeId, int $taskId): void {
        if (!$this->auth) return;
        $user = $this->auth->getUserById($assigneeId);
        if (!$user || empty($user['email'])) return;
        @mail(
            $user['email'],
            'You were assigned a task',
            "You have been assigned to task #$taskId.",
            "From: no-reply@{$_SERVER['HTTP_HOST']}\r\n"
        );
    }

}
