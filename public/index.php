<?php

use App\Repositories\{
    AuthRepository,
    ProjectRepository,
    TaskRepository,
    UserRepository
};

use App\Services\{
    AuthService,
    ProjectService,
    TaskService,
    UserService
};

use App\Controllers\{
    AuthController,
    LoginController,
    HomeController,
    ProjectController
};

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: *"); // Allows any website (*) to access this API (useful for public APIs).
header("Access-Control-Allow-Headers: *"); // Allows all HTTP request headers (useful for handling JSON requests, auth tokens, etc.).

// Set up environment variables
$dotenv = Dotenv::createImmutable(dirname('/'));
$dotenv->load();

$dotenv->required(['SITE_URL']);

$serverIp = $_SERVER['SERVER_ADDR'];
define('SITE_URL', $_ENV['SITE_URL']);

$uri = trim($_SERVER['REQUEST_URI'], '/');

// -------------------- DI Container setup --------------------
// Repositories
$authRepo = new AuthRepository();
$projectRepo = new ProjectRepository();
$taskRepo = new TaskRepository();
$userRepo = new UserRepository();

// Services
$authService = new AuthService($authRepo);
$projectService = new ProjectService($projectRepo);
$taskService = new TaskService($taskRepo);
$userService = new UserService($userRepo);

// Controllers
$authController = new AuthController($authService);
$homeController = new HomeController($authService, $projectService);
$loginController = new LoginController($authService);
$projectController = new ProjectController($authService, $projectService, $taskService);

// Controller map for router
$controllers = [
    'LoginController' => $loginController,
    'HomeController' => $homeController,
    'ProjectController' => $projectController,
    'AuthController' => $authService
];

// Pass controller map to router to handle Dependency Injection (DI)
$router = new App\Router($controllers);
$router->route($uri);