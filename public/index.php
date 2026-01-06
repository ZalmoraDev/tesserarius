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

require_once dirname(__DIR__) . '/vendor/autoload.php';

// TODO: Consider moving this to middleware / API router
header("Access-Control-Allow-Methods: GET, POST"); // Only allow GET and POST requests.
header("Access-Control-Allow-Origin: *"); // TODO: Change this to localhost
header("Access-Control-Allow-Headers: *"); // Allows all HTTP request headers (useful for handling JSON requests, auth tokens, etc.).

// @vlucas/phpdotenv | https://packagist.org/packages/vlucas/phpdotenv
// Set up environment variables, autoload /.env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(['SITE_URL', 'DB_TYPE', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);

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
    'auth' => $authController,
    'home' => $homeController,
    'login' => $loginController,
    'project' => $projectController
];

// Pass controller map to router to handle Dependency Injection (DI)
$router = new App\Router($controllers);
$router->dispatch();