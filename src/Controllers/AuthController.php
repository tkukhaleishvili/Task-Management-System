<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\TaskService;
use App\Repositories\PdoTaskRepository;

class AuthController
{
    private AuthService $auth;
    private TaskService $tasks;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
        // initialize TaskService with its repository
        $this->tasks = new TaskService(new PdoTaskRepository());
    }

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

    private function consumeRemember(string $cookie): ?int
    {
        $result = $this->auth->consumeRememberMe($cookie);
        if (!$result) return null;
        $_SESSION['user_id'] = $result['user_id'];
        $c = $result['remember_cookie'];
        setcookie($c['cookie_name'], $c['cookie_value'], $c['expires_at']->getTimestamp(), '/', '', true, true);
        return $result['user_id'];
    }

    private function requireAuth(): int
    {
        if (!empty($_SESSION['user_id'])) return (int)$_SESSION['user_id'];
        if (!empty($_COOKIE['remember'])) {
            $uid = $this->consumeRemember($_COOKIE['remember']);
            if ($uid) return $uid;
        }
        $this->flash('error', 'Please log in.');
        $this->redirect('/login');
        return 0;
    }

    public function dashboard(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->flash('error', 'Please log in.');
            $this->redirect('/login');
        }

        $user = $this->auth->getUserById((int)$userId);

        $taskService = new \App\Services\TaskService(new \App\Repositories\PdoTaskRepository(), $this->auth);
        $tasks = $taskService->getTasksByUser((int)$userId);

        include __DIR__ . '/../../templates/pages/dashboard.php';
    }

    public function showLogin(): void
    {
        $lastUser = null;
        if (!empty($_COOKIE['last_user'])) {
            $lastUser = json_decode($_COOKIE['last_user'], true);
        }

        $this->render('auth/login', [
            'csrf'     => $this->csrfToken(),
            'error'    => $this->takeFlash('error'),
            'success'  => $this->takeFlash('success'),
            'lastUser' => $lastUser,
        ]);

    }

    public function login(): void
    {
        $this->checkCsrf();
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $res = $this->auth->login($login, $password, $remember);
        if (!$res) {
            $this->flash('error', 'Invalid email/username or password.');
            $this->redirect('/login');
        }

        $_SESSION['user_id'] = $res['user']['id'];

        setcookie(
            'last_user',
            json_encode([
                'id' => $res['user']['id'],
                'username' => $res['user']['username'] ?? '',
                'email' => $res['user']['email'] ?? ''
            ]),
            [
                'expires'  => time() + (60 * 60 * 24 * 30), 
                'path'     => '/',
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'httponly' => false, 
                'samesite' => 'Lax',
            ]
            );

        $this->flash('success', 'Welcome back!');
        $this->redirect('/');
    }

    public function logout(): void
    {
        $this->checkCsrf();

        if (!empty($_SESSION['user_id'])) {
            $this->auth->logout((int)$_SESSION['user_id']);
        }

        $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
            }
            setcookie('remember', '', time() - 3600, '/');
            session_destroy();

        $this->flash('success', 'Logged out.');
        $this->redirect('/login');
    }

    public function showRegister(): void
    {
        $this->render('auth/register', [
            'csrf'  => $this->csrfToken(),
            'error' => $this->takeFlash('error'),
        ]);
    }

    public function register(): void
    {
        $this->checkCsrf();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        try {
            $id = $this->auth->register($username, $email, $password);
            $_SESSION['user_id'] = $id;
            $this->flash('success', 'Registration successful.');
            $this->redirect('/');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/register');
        }
    }
}
