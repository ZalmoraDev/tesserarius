<?php

use App\CsrfService;

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

// -------------------- headers, session & .env config --------------------
// TODO: Consider moving this to middleware / API router
header("Access-Control-Allow-Methods: GET, POST"); // Only allow GET and POST requests.
header("Access-Control-Allow-Origin: *"); // TODO: Change this to localhost
header("Access-Control-Allow-Headers: *"); // Allows all HTTP request headers (useful for handling JSON requests, auth tokens, etc.).

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // https://www.php.net/manual/en/session.configuration.php
    session_start([
        'use_strict_mode' => true, // prevent CSRF & uninitialized session IDs
        'cookie_httponly' => true, // prevent JS access to cookies (XSS)
        'cookie_samesite' => 'Strict', // prevent cross-site usage
    ]);
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
$homeController = new HomeController($projectService);
$projectController = new ProjectController($projectService, $taskService);

// Controller map for router
$controllers = [
    'auth' => $authController,
    'home' => $homeController,
    'project' => $projectController
];

// Pass controller map to router to serve as handlers

$router = new App\Router($controllers);
$router->dispatch();