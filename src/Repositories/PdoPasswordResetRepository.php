<?php
namespace App\Repositories;

use App\Repositories\Interfaces\PasswordResetRepositoryInterface;
use App\Utilities\Connection;
use PDO;

class PdoPasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function __construct(private ?PDO $pdo = null) { $this->pdo ??= Connection::get(); }

    public function create(int $userId, string $tokenHash, \DateTimeInterface $expiresAt): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:u,:h,:e)"
        );
        $stmt->execute([
            ':u'=>$userId, ':h'=>$tokenHash, ':e'=>$expiresAt->format('Y-m-d H:i:s')
        ]);
    }

    public function findValid(int $userId, string $tokenHash): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM password_resets
             WHERE user_id = :u AND token_hash = :h AND used_at IS NULL AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([':u'=>$userId, ':h'=>$tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markUsed(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id");
        $stmt->execute([':id'=>$id]);
    }

    public function purgeExpired(): void
    {
        $this->pdo->exec("DELETE FROM password_resets WHERE expires_at <= NOW()");
    }
}
