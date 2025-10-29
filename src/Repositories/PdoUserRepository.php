<?php
namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Utilities\Connection;
use PDO;

class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(private ?PDO $pdo = null) { $this->pdo ??= Connection::get(); }

    public function create(string $username, string $email, string $passwordHash, string $role): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, email, password_hash, role, created_at) VALUES (:u,:e,:p,:r,NOW())"
        );
        $stmt->execute([':u'=>$username, ':e'=>$email, ':p'=>$passwordHash, ':r'=>$role]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :e LIMIT 1");
        $stmt->execute([':e'=>$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function emailOrUsernameExists(string $email, string $username): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE email = :e OR username = :u LIMIT 1");
        $stmt->execute([':e'=>$email, ':u'=>$username]);
        return (bool)$stmt->fetchColumn();
    }

    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = :p WHERE id = :id");
        return $stmt->execute([':p'=>$passwordHash, ':id'=>$userId]);
    }
}
