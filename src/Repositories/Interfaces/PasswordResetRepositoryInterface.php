<?php
namespace App\Repositories\Interfaces;

interface PasswordResetRepositoryInterface
{
    public function create(int $userId, string $tokenHash, \DateTimeInterface $expiresAt): void;
    public function findValid(int $userId, string $tokenHash): ?array; // returns row if not used and not expired
    public function markUsed(int $id): void;
    public function purgeExpired(): void;
}
