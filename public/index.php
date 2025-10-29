<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Repositories\PdoTaskRepository;
use App\Repositories\PdoUserRepository;
use App\Repositories\PdoRememberTokenRepository;
use App\Repositories\PdoPasswordResetRepository;

use App\Services\TaskService;
use App\Services\AuthService;

$routes = require __DIR__ . '/../config/routes.php';
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

session_start();
function makeController(string $controllerClass) {
    switch ($controllerClass) {
        case \App\Controllers\TaskController::class:
            $taskRepo = new PdoTaskRepository();
            $authSvc = new \App\Services\AuthService(
                new \App\Repositories\PdoUserRepository(),
                new \App\Repositories\PdoRememberTokenRepository(),
                new \App\Repositories\PdoPasswordResetRepository()
            );

            $taskSvc = new \App\Services\TaskService($taskRepo, $authSvc);
            return new $controllerClass($taskSvc);

        case \App\Controllers\AuthController::class:
        case \App\Controllers\PasswordResetController::class:
            $authSvc = new AuthService(
                users:           new PdoUserRepository(),
                rememberTokens:  new PdoRememberTokenRepository(),
                passwordResets:  new PdoPasswordResetRepository()
            );
            return new $controllerClass($authSvc);
        default:
            return new $controllerClass();
    }
}

if (!isset($routes[$method]) || !isset($routes[$method][$uri])) {
    http_response_code(404);
    echo "404 - Page not found";
    exit;
}

[$controllerClass, $action] = $routes[$method][$uri];
$controller = makeController($controllerClass);

switch ([$method, $uri, $action]) {
    case ['GET', '/task', 'show']:
    case ['GET', '/tasks/edit', 'edit']:
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->$action($id);
        break;

    case ['POST', '/tasks/update', 'update']:
    case ['POST', '/tasks/delete', 'destroy']:
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $controller->$action($id);
        break;

    default:
        $controller->$action();
}
