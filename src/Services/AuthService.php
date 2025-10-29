<?php
namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RememberTokenRepositoryInterface;
use App\Repositories\Interfaces\PasswordResetRepositoryInterface;

final class AuthService
{
    public function __construct(
        private UserRepositoryInterface $users,
        private RememberTokenRepositoryInterface $rememberTokens,
        private PasswordResetRepositoryInterface $passwordResets
    ) {}

    public function register(string $username, string $email, string $password, string $role = 'User'): int
    {
        $username = trim($username);
        $email    = strtolower(trim($email));

        if ($this->users->emailOrUsernameExists($email, $username)) {
            throw new \RuntimeException('Username or email already taken.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->users->create($username, $email, $hash, $role);
    }

    public function login(string $emailOrUsername, string $password, bool $remember = false): ?array
    {
        $user = $this->users->findByEmail($emailOrUsername)
             ?: (method_exists($this->users, 'findByUsername') ? $this->users->findByUsername($emailOrUsername) : null);

        if (!$user) {
            return null;
        }
        if (!password_verify($password, $user['password_hash'] ?? $user->password_hash ?? '')) {
            return null;
        }

        $result = ['user' => $user];

        if ($remember) {
            $result['remember_cookie'] = $this->issueRememberMeToken((int)$user['id']);
        }

        return $result;
    }

    public function logout(int $userId): void
    {
        $this->rememberTokens->deleteByUser($userId);
    }

    public function issueRememberMeToken(int $userId, \DateInterval $ttl = new \DateInterval('P30D')): array
    {
        $selector  = rtrim(strtr(base64_encode(random_bytes(9)), '+/', '-_'), '=');
        $validator = rtrim(strtr(base64_encode(random_bytes(33)), '+/', '-_'), '=');
        $hash      = hash('sha256', $validator);

        $expiresAt = (new \DateTimeImmutable('now'))->add($ttl);
        $this->rememberTokens->create($userId, $selector, $hash, $expiresAt);

        return [
            'cookie_name'  => 'remember',
            'cookie_value' => $selector . ':' . $validator,
            'expires_at'   => $expiresAt,
        ];
    }

    public function consumeRememberMe(string $cookieValue): ?array
    {
        if (!str_contains($cookieValue, ':')) {
            return null;
        }
        [$selector, $validator] = explode(':', $cookieValue, 2);

        $tokenRow = $this->rememberTokens->findValidBySelector($selector);
        if (!$tokenRow) {
            return null;
        }
        $match = hash_equals($tokenRow['validator_hash'], hash('sha256', $validator));
        if (!$match) {
            $this->rememberTokens->deleteBySelector($selector);
            return null;
        }

        $this->rememberTokens->deleteBySelector($selector);
        $newCookie = $this->issueRememberMeToken((int)$tokenRow['user_id']);

        return [
            'user_id'       => (int)$tokenRow['user_id'],
            'remember_cookie' => $newCookie,
        ];
    }

    public function startPasswordReset(string $email, \DateInterval $ttl = new \DateInterval('PT30M')): ?array
    {
        $user = $this->users->findByEmail(strtolower(trim($email)));
        if (!$user) {
            return null; 
        }

        $rawToken  = rtrim(strtr(base64_encode(random_bytes(33)), '+/', '-_'), '=');
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = (new \DateTimeImmutable('now'))->add($ttl);

        $this->passwordResets->create((int)$user['id'], $tokenHash, $expiresAt);

        return [
            'user'        => $user,
            'token'       => $rawToken, 
            'expires_at'  => $expiresAt,
        ];
    }

    public function completePasswordReset(string $rawToken, string $newPassword): bool
    {
        $tokenHash = hash('sha256', $rawToken);
        $row = $this->passwordResets->findValidByHash($tokenHash);
        if (!$row) {
            return false;
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $ok = $this->users->updatePassword((int)$row['user_id'], $hash);

        if ($ok) {
            $this->passwordResets->markUsed((int)$row['id']);
            $this->rememberTokens->deleteByUser((int)$row['user_id']);
        }
        return $ok;
    }

    public function getUserById(int $id): ?array
    {
        return $this->users->findById($id);
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->users->findByEmail(strtolower(trim($email)));
    }

    public function purgeExpiredTokens(): void
    {
        $this->rememberTokens->purgeExpired();
        $this->passwordResets->purgeExpired();
    }
}
