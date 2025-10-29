<?php
namespace App\Controllers;

use App\Services\TaskService;

class TaskController
{
    private TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function show(int $id): void
    {
        $task = $this->service->getTaskById($id);
        include __DIR__ . '/../../templates/pages/task_detail.php';
    }

    public function create(): void
    {
        include __DIR__ . '/../../templates/pages/task_create.php';
    }

    public function store(): void
    {
        try {
            $task = $this->service->createTask($_POST);
            header('Location: /?created=' . $task->getId());
            exit;
        } catch (\InvalidArgumentException $e) {
            $error = $e->getMessage();
            include __DIR__ . '/../../templates/pages/task_create.php';
        } catch (\Exception $e) {
            $error = 'Unexpected error: ' . $e->getMessage();
            include __DIR__ . '/../../templates/pages/task_create.php';
        }
    }

    public function edit(int $id): void {
        $task = $this->service->getTaskById($id);
        if (!$task) { http_response_code(404); echo "Not found"; return; }
        include __DIR__ . '/../../templates/pages/task_edit.php';
    }

    public function update(): void {
        try {
            $id = (int)($_POST['id'] ?? 0);
            $actorId = (int)($_SESSION['user_id'] ?? 0);
            $data = [
              'title' => trim($_POST['title'] ?? ''),
              'description' => trim($_POST['description'] ?? ''),
              'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
              'assigned_to' => (int)($_POST['assigned_to'] ?? 0) ?: null,
              'priority' => $_POST['priority'] ?? null,
              'due_date' => $_POST['due_date'] ?? null,
              'status'   => $_POST['status'] ?? null,
            ];
            $this->service->updateTask($id, $data, $actorId);
            header('Location: /task?id='.$id.'&updated=1'); exit;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            $task = $this->service->getTaskById((int)$_POST['id']);
            include __DIR__ . '/../../templates/pages/task_edit.php';
        }
    }

    public function destroy(): void {
        $id = (int)($_POST['id'] ?? 0);
        $actorId = (int)($_SESSION['user_id'] ?? 0);
        try {
            $this->service->deleteTask($id, $actorId);
        $_SESSION['flash']['success'] = "Task #$id deleted successfully.";
        header('Location: /');
        exit;

        } catch (\Throwable $e) {
            header('Location: /task?id='.$id.'&error='.urlencode($e->getMessage())); exit;
        }
    }

    public function changeStatus(): void {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'Pending';
        $actorId = (int)($_SESSION['user_id'] ?? 0);
        $this->service->changeStatus($id, $status, $actorId);
        header('Location: /task?id='.$id.'&status_changed=1'); exit;
    }

    public function addComment(): void {
        $id = (int)($_POST['id'] ?? 0);
        $actorId = (int)($_SESSION['user_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        try {
            $this->service->addComment($id, $actorId, $comment);
            header('Location: /task?id='.$id.'#comments'); exit;
        } catch (\Throwable $e) {
            header('Location: /task?id='.$id.'&error='.urlencode($e->getMessage())); exit;
        }
    }

    public function attachFile(): void {
        $id = (int)($_POST['id'] ?? 0);
        $actorId = (int)($_SESSION['user_id'] ?? 0);
        try {
            $this->service->attachFile($id, $_FILES['file'] ?? [], $actorId);
            header('Location: /task?id='.$id.'#attachments'); exit;
        } catch (\Throwable $e) {
            header('Location: /task?id='.$id.'&error='.urlencode($e->getMessage())); exit;
        }
    }

    public function index(): void {
       
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];

     
        $authService = new \App\Services\AuthService(
            new \App\Repositories\PdoUserRepository(),
            new \App\Repositories\PdoRememberTokenRepository(),
            new \App\Repositories\PdoPasswordResetRepository()
        );
        $user = $authService->getUserById($userId);

    
        $filters = [
            'assigned_to' => $userId,
            'status'      => $_GET['status'] ?? null,
            'category_id' => $_GET['category_id'] ?? null,
            'q'           => $_GET['q'] ?? null,
            'sort'        => $_GET['sort'] ?? null,
        ];
        $tasks = $this->service->listTasksFiltered($filters);

 
        $success = $_SESSION['flash']['success'] ?? null;
        unset($_SESSION['flash']['success']);

        include __DIR__ . '/../../templates/pages/dashboard.php';
    }


}
