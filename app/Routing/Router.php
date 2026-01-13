<?php

namespace App\Routing;

use App\Core\Csrf;

use App\Models\Enums\AccessRole;
use App\Controllers;
use App\Services\AuthServiceInterface;

use FastRoute;

final class Router
{
    // Dependency Injection of FastRoute dispatcher routes, created in Routes.php
    private FastRoute\Dispatcher $dispatcher;
    private AuthServiceInterface $authService;

    public function __construct(FastRoute\Dispatcher $dispatcher, AuthServiceInterface $authService)
    {
        $this->dispatcher = $dispatcher;
        $this->authService = $authService;
    }

    /** nikic/fast-route | https://packagist.org/packages/nikic/fast-route.
     * Modification on the basic usage implementation from docs, contains additional header data regarding route Access rights. */
    public function dispatch(): void
    {
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];


        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        // Use dispatcher retrieved from Routes.php
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        $errorController = new Controllers\ErrorController();
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $errorController->notFound();
                exit;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $errorController->methodNotAllowed();
                exit;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // Abbreviate required access role for this route AND retrieve auth service
                $requiredAccess = $handler['accessRole'];

                // AUTHENTICATION: If route requires authenticated user, but user is not authenticated, redirect to /login
                if ($requiredAccess >= AccessRole::Authenticated &&
                    $this->authService->isAuthenticated() === false) {
                    $_SESSION['flash_errors'][] = 'requires_login';
                    header('Location: /login', true, 302);
                    exit;
                }

                // TODO: Validate if this actually works as intended

                // AUTHORIZATION: If route requires higher role then is accessing, redirect to / (homePage)
                if ($requiredAccess >= AccessRole::Member &&
                    $this->authService->isAccessAuthorized($vars['projectId']) === false) {
                    $_SESSION['flash_errors'][] = 'not_authorized';
                    header('Location: /', true, 403);
                    exit;
                }

                // POST CSRF: Upon POST -> verify CSRF token. If not valid, exit with 403 (handled in Csrf::Verify)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    Csrf::Verify($_POST['csrf'] ?? null);
                }

                // (If no authentication was required OR user passed auth guards) & CSRF token is validated -> call handler
                // METHOD + URI → handler + params
                call_user_func_array($handler['handler'], $vars);
                break;
        }
    }
}