<?php

use App\Core\Csp;
use App\Routing\{Routes, Router};

use App\Controllers\{Api\TaskApiController,
    AuthController,
    ProjectMembersController,
    UserController,
    ProjectController
};
use App\Services\{AuthService, ProjectMembersService, ProjectService, TaskService, UserService};
use App\Repositories\{AuthRepository, ProjectMembersRepository, ProjectRepository, TaskRepository, UserRepository};

// -------------------- Error reporting --------------------
// (Uncomment for debugging)
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


// -------------------- Session & .env config --------------------
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

/** vlucas/phpdotenv, set up environment variables, autoload /.env file
 * @see https://packagist.org/packages/vlucas/phpdotenv
 */
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    'SITE_NAME', 'SITE_URL',
    'DB_TYPE', 'DB_HOST', 'DB_PORT', 'DB_DATABASE',
    'DB_USERNAME', 'DB_PASSWORD']);

// -------------------- Security Headers --------------------
// See HTTP headers:
// 1) https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference
// 2) https://developer.mozilla.org/en-US/docs/Glossary/Fetch_directive
// 3) https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy/script-src#unsafe_inline_script

header("Access-Control-Allow-Methods: GET, POST"); // Only allow GET and POST requests
header("Access-Control-Allow-Origin: " . $_ENV['SITE_URL']); // Only allow requests from this host's URL
header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' 'nonce-" . Csp::getNonce() . "';"); // CSP to mitigate XSS attacks
header("X-Content-Type-Options: nosniff"); // Prevent MIME type sniffing
header("X-Frame-Options: SAMEORIGIN"); // Prevent clickjacking
header("Referrer-Policy: strict-origin-when-cross-origin"); // Control referrer information

// -------------------- DI Container setup --------------------
// Repositories
$authRepo = new AuthRepository();
$projectRepo = new ProjectRepository();
$projectMembersRepo = new ProjectMembersRepository();
$taskRepo = new TaskRepository();
$userRepo = new UserRepository();

// Services
$authService = new AuthService($authRepo, $userRepo);
$projectService = new ProjectService($projectRepo, $projectMembersRepo);
$projectMembersService = new ProjectMembersService($projectMembersRepo);
$taskService = new TaskService($taskRepo);
$userService = new UserService($userRepo);

// ControllersWeb
$authController = new AuthController($authService);
$projectController = new ProjectController($projectService, $projectMembersService, $taskService);
$projectMembersController = new ProjectMembersController($projectMembersService);
$userController = new UserController($userService, $projectService);
// ControllersAPI
$taskApi = new TaskApiController($authService, $taskService);

// -------------------- Routing setup & Router dispatch --------------------
// Controller map for Routes.php
$controllers = [
    'auth' => $authController,
    'project' => $projectController,
    'projectMembers' => $projectMembersController,
    'user' => $userController,
    'taskApi' => $taskApi,
];

$routes = new Routes($controllers);
$router = new Router($routes->dispatcher(), $authService);
$router->dispatch();