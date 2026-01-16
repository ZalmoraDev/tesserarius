<?php

use App\Routing\{Routes, Router};

use App\Controllers\ {AuthController, DashboardController, ProjectController};
use App\Services\{AuthService, ProjectService, TaskService};
use App\Repositories\{AuthRepository, ProjectRepository, TaskRepository, UserRepository};

// -------------------- Headers, Session & .env config --------------------
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
$dotenv->required([
    'SITE_NAME', 'SITE_URL',
    'DB_TYPE', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
    'DB_USERNAME', 'DB_PASSWORD']);

// -------------------- DI Container setup --------------------
// Repositories
$authRepo = new AuthRepository();
$projectRepo = new ProjectRepository();
$taskRepo = new TaskRepository();
$userRepo = new UserRepository();

// Services
$authService = new AuthService($authRepo, $userRepo);
$projectService = new ProjectService($projectRepo);
$taskService = new TaskService($taskRepo);

// Controllers
$authController = new AuthController($authService);
$dashboardController = new DashboardController($projectService);
$projectController = new ProjectController($projectService, $taskService);

// -------------------- Routing setup & Router dispatch --------------------
// Controller map for router
$controllers = [
    'auth' => $authController,
    'dashboard' => $dashboardController,
    'project' => $projectController
];

$routes = new Routes($controllers);
$router = new Router($routes->dispatcher(), $authService);
$router->dispatch();