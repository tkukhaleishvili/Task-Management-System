<?php
namespace App\Repositories;

use App\Repositories\Interfaces\RememberTokenRepositoryInterface;
use App\Utilities\Connection;
use PDO;

class PdoRememberTokenRepository implements RememberTokenRepositoryInterface
{
    public function __construct(private ?PDO $pdo = null) { $this->pdo ??= Connection::get(); }

    public function create(int $userId, string $selector, string $validatorHash, \DateTimeInterface $expiresAt): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO remember_tokens (user_id, selector, validator_hash, expires_at)
             VALUES (:u,:s,:h,:e)"
        );
        $stmt->execute([
            ':u'=>$userId, ':s'=>$selector, ':h'=>$validatorHash,
            ':e'=>$expiresAt->format('Y-m-d H:i:s')
        ]);
    }

    public function findBySelector(string $selector): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM remember_tokens WHERE selector = :s LIMIT 1");
        $stmt->execute([':s'=>$selector]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteBySelector(string $selector): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE selector = :s");
        $stmt->execute([':s'=>$selector]);
    }

    public function deleteByUser(int $userId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE user_id = :u");
        $stmt->execute([':u'=>$userId]);
    }

    public function purgeExpired(): void
    {
        $this->pdo->exec("DELETE FROM remember_tokens WHERE expires_at <= NOW()");
    }
}
