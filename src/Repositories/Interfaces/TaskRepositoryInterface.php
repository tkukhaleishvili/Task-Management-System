<?php
namespace App\Repositories\Interfaces;

use App\Models\Task;

interface TaskRepositoryInterface
{
    public function create(array $data): Task;
    public function findById(int $id): ?Task;
    public function findAll(): array;
    public function findByUser(int $userId): array;
    public function update(int $id, array $data): bool;

    public function softDelete(int $id): bool;
    public function changeStatus(int $id, string $status): bool;
    public function findWithExtras(int $id): ?array; 
    public function findByFilters(array $filters): array; 
}
