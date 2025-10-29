<?php
namespace App\Models;

use App\Models\Traits\Timestampable;

class Task extends BaseModel
{
    use Timestampable;

    protected ?int $id = null;
    protected string $title = '';
    protected ?string $description = null;
    protected ?int $category_id = null;
    protected ?int $assigned_to = null;
    protected ?int $created_by = null;
    protected string $priority = 'Medium';
    protected string $status = 'Pending';
    protected ?string $due_date = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = trim($title); }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): void { $this->description = $desc; }

    public function getCategoryId(): ?int { return $this->category_id; }
    public function setCategoryId(?int $id): void { $this->category_id = $id; }

    public function getAssignedTo(): ?int { return $this->assigned_to; }
    public function setAssignedTo(?int $userId): void { $this->assigned_to = $userId; }

    public function getCreatedBy(): ?int { return $this->created_by; }
    public function setCreatedBy(?int $userId): void { $this->created_by = $userId; }

    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $priority): void {
        $allowed = ['Low', 'Medium', 'High', 'Critical'];
        if (!in_array($priority, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid priority: $priority");
        }
        $this->priority = $priority;
    }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void {
        $allowed = ['Pending', 'In Progress', 'Completed'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid status: $status");
        }
        $this->status = $status;
    }

    public function getDueDate(): ?string { return $this->due_date; }
    public function setDueDate(?string $date): void { $this->due_date = $date; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $ts): void { $this->created_at = $ts; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $ts): void { $this->updated_at = $ts; }

    public function validate(): bool
    {
        if (empty($this->title)) {
            throw new \InvalidArgumentException('Task title is required.');
        }
        if (empty($this->created_by)) {
            throw new \InvalidArgumentException('Task must have a creator.');
        }
        return true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function markCompleted(): void
    {
        $this->status = 'Completed';
        $this->touch(); 
    }
}
