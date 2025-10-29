<?php
namespace App\Repositories\Interfaces;

interface RememberTokenRepositoryInterface
{
    public function create(int $userId, string $selector, string $validatorHash, \DateTimeInterface $expiresAt): void;
    public function findBySelector(string $selector): ?array; 
    public function deleteBySelector(string $selector): void;
    public function deleteByUser(int $userId): void;
    public function purgeExpired(): void;
}
