<?php
namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function create(string $username, string $email, string $passwordHash, string $role): int;
    public function findByEmail(string $email): ?array;   
    public function findById(int $id): ?array;           
    public function emailOrUsernameExists(string $email, string $username): bool;
    public function updatePassword(int $userId, string $passwordHash): bool;
}
