<?php
namespace App\Repositories;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use App\Utilities\Connection;
use PDO;

class PdoTaskRepository implements TaskRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }
    public function create(array $data): Task
    {
        if (empty($data['title'])) {
            throw new \InvalidArgumentException('title is required');
        }
        if (!isset($data['created_by']) || !is_numeric($data['created_by'])) {
            throw new \InvalidArgumentException('created_by is required and must be numeric');
        }

        $title       = trim((string)$data['title']);
        $description = isset($data['description']) && $data['description'] !== '' ? (string)$data['description'] : null;

        $categoryId  = isset($data['category_id']) && $data['category_id'] !== '' ? (int)$data['category_id'] : null;
        $assignedTo  = isset($data['assigned_to']) && $data['assigned_to'] !== '' ? (int)$data['assigned_to'] : null;

        $createdBy   = (int)$data['created_by'];

        $priority    = isset($data['priority']) && $data['priority'] !== '' ? (string)$data['priority'] : 'Medium';
        $status      = isset($data['status'])   && $data['status']   !== '' ? (string)$data['status']   : 'Pending';

        $dueDate     = isset($data['due_date']) && $data['due_date'] !== '' ? (string)$data['due_date'] : null;

        $sql = "INSERT INTO tasks
                (title, description, category_id, assigned_to, created_by, priority, status, due_date)
                VALUES
                (:title, :description, :category_id, :assigned_to, :created_by, :priority, :status, :due_date)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':category_id' => $categoryId,
            ':assigned_to' => $assignedTo,
            ':created_by'  => $createdBy,
            ':priority'    => $priority,
            ':status'      => $status,
            ':due_date'    => $dueDate,
        ]);

        $id = (int)$this->pdo->lastInsertId();
        return $this->findById($id);
    }

    public function findById(int $id): ?Task
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, 
                   c.name AS category_name, 
                   u.username AS assigned_user 
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Task($row) : null;
    }


    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, c.name AS category_name, u.username AS assigned_user
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.assigned_to = :uid
            AND t.is_deleted = 0
            ORDER BY t.due_date ASC
        ");
        $stmt->execute(['uid' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new \App\Models\Task($r), $rows);
    }

    public function findAll(): array
        {
            $sql = "
                SELECT t.*, 
                       c.name AS category_name, 
                       u.username AS assigned_user 
                FROM tasks t
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.is_deleted = 0
                ORDER BY t.due_date ASC
            ";
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return array_map(fn($r) => new \App\Models\Task($r), $rows);
        }


    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [':id' => $id];
        foreach (['title','description','category_id','assigned_to','priority','due_date','status'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = :$col";
                $params[":$col"] = $data[$col];
            }
        }
        if (!$fields) return false;
        $sql = "UPDATE tasks SET ".implode(',', $fields).", updated_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        return $this->pdo->prepare($sql)->execute($params);
    }

    public function softDelete(int $id): bool {
        $stmt = $this->pdo->prepare("
            UPDATE tasks
            SET deleted_at = NOW(),
                is_deleted = 1
            WHERE id = :id AND is_deleted = 0
        ");
        return $stmt->execute([':id' => $id]);
    }


    public function changeStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE tasks SET status = :s, updated_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        return $stmt->execute([':s'=>$status, ':id'=>$id]);
    }

    public function findWithExtras(int $id): ?array {
        $stmt = $this->pdo->prepare("
          SELECT t.*, c.name AS category_name, u.username AS assigned_user
          FROM tasks t
          LEFT JOIN categories c ON t.category_id=c.id
          LEFT JOIN users u ON t.assigned_to=u.id
          WHERE t.id=:id AND t.deleted_at IS NULL
          LIMIT 1
        ");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByFilters(array $f): array {
        $where = ["t.is_deleted = 0"];
        $params = [];

        if (!empty($f['assigned_to'])) { 
            $where[] = "t.assigned_to = :at"; 
            $params[':at'] = (int)$f['assigned_to']; 
        }
        if (!empty($f['status'])) { 
            $where[] = "t.status = :st";      
            $params[':st'] = $f['status']; 
        }
        if (!empty($f['category_id'])) { 
            $where[] = "t.category_id = :cid"; 
            $params[':cid'] = (int)$f['category_id']; 
        }
        if (!empty($f['q'])) { 
            $where[] = "(t.title LIKE :q OR t.description LIKE :q)"; 
            $params[':q'] = "%{$f['q']}%"; 
        }

        $order = "t.due_date ASC";
        if (!empty($f['sort'])) {
            $map = [
              'due_asc'=>"t.due_date ASC",
              'due_desc'=>"t.due_date DESC",
              'prio'=>"FIELD(t.priority,'Critical','High','Medium','Low') ASC",
              'created_desc'=>"t.created_at DESC"
            ];
            if (isset($map[$f['sort']])) $order = $map[$f['sort']];
        }

        $sql = "
          SELECT t.*, c.name AS category_name, u.username AS assigned_user
          FROM tasks t
          LEFT JOIN categories c ON t.category_id = c.id
          LEFT JOIN users u ON t.assigned_to = u.id
          WHERE " . implode(' AND ', $where) . "
          ORDER BY $order
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new \App\Models\Task($r), $rows);
    }

}
