<?php
namespace App\Controllers;

use App\Services\AuthService;

class PasswordResetController
{
    public function __construct(private AuthService $auth) {}

    private function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf'];
    }

    private function checkCsrf(): void
    {
        $ok = isset($_POST['_csrf']) && hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf']);
        if (!$ok) {
            http_response_code(419);
            echo "CSRF token mismatch";
            exit;
        }
    }

    private function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }

    private function flash(string $key, string $msg): void
    {
        $_SESSION['flash'][$key] = $msg;
    }

    private function takeFlash(string $key): ?string
    {
        if (!empty($_SESSION['flash'][$key])) {
            $m = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $m;
        }
        return null;
    }

    private function render(string $view, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
                $viewFile = __DIR__ . "/../../templates/pages/{$view}.php";
        include __DIR__ . "/../../templates/pages/_layout.php";
    }

    public function showRequest(): void
    {
        $this->render('auth/request_reset', [
            'csrf' => $this->csrfToken(),
            'success' => $this->takeFlash('success'),
            'error' => $this->takeFlash('error'),
        ]);
    }

    public function request(): void
    {
        $this->checkCsrf();
        $email = trim($_POST['email'] ?? '');
        $res = $this->auth->startPasswordReset($email);

       if ($res) {
            $resetLink = sprintf(
                "%s/password/reset?token=%s",
                $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
                urlencode($res['token'])
            );

            $to = $email;
            $subject = "Password Reset Request";
            $message = "Hello,\n\nWe received a password reset request for your account.\n\n".
                       "Click the link below to reset your password:\n$resetLink\n\n".
                       "If you did not request this, simply ignore this email.\n\n".
                       "Best regards,\nTask Management System";
            $headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
            if (mail($to, $subject, $message, $headers)) {
                $this->flash('success', 'Reset link sent to your email.');
            } else {
                $this->flash('error', 'Failed to send reset email (mail() not configured).');
            }
        } else {
            $this->flash('success', 'If that email exists, a reset link was sent.');
        }


        $this->redirect('/password/forgot');
    }

    public function showReset(): void
    {
        $token = $_GET['token'] ?? '';
        $this->render('auth/reset', [
            'csrf' => $this->csrfToken(),
            'token' => $token,
            'success' => $this->takeFlash('success'),
            'error' => $this->takeFlash('error'),
        ]);
    }

    public function reset(): void
    {
        $this->checkCsrf();
        $token = $_POST['token'] ?? '';
        $new = $_POST['password'] ?? '';

        $ok = $this->auth->completePasswordReset($token, $new);
        if (!$ok) {
            $this->flash('error', 'Invalid or expired reset token.');
            $this->redirect('/password/reset?token=' . urlencode($token));
        }

        $this->flash('success', 'Password updated. Please log in.');
        $this->redirect('/login');
    }
}
