<?php

namespace App\Routing;

use App\Core\Csrf;

use App\Controllers;
use App\Services\AuthServiceInterface;

use App\Services\Exceptions\AuthException;
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
     * Modification on the basic usage implementation from docs, contains additional header data regarding route access rights. */
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
                try {
                    $handler = $routeInfo[1];
                    $pathParams = $routeInfo[2];
                    $routeReqAccess = $handler['accessRole'];

                    // Checks if user is logged in for pages that require authentication
                    $this->authService->requireAuthentication($routeReqAccess);

                    // Checks if the already logged-in user is visiting the loginPage OR signupPage, redirect to / (home)
                    $this->authService->denyAuthenticatedOnAuthRoutes($handler['action'][1]);

                    // Checks when accessing a project-related route, if user has access to it with required role or higher

                    // TODO: NOT WORKING, 'member' role can access 'admin' / 'owner' routes
                    if ($pathParams['projectId'] ?? false)
                        $this->authService->requireProjectAccess((int)$pathParams['projectId'], $routeReqAccess);

                    // Upon POST -> verify CSRF token. If not valid, exit with 403 (handled in Csrf::Verify)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST')
                        Csrf::requireVerification($_POST['csrf'] ?? null);

                    // If no auth was required OR user passed project auth guards
                    // AND CSRF token on POST is validated -> call handler
                    call_user_func_array($handler['action'], $pathParams);
                    break;
                } catch (AuthException $e) {
                    $_SESSION['flash_errors'][] = $e->getMessage();
                    switch ($e->getMessage()) {
                        case AuthException::REQUIRES_LOGIN:
                        case AuthException::CSRF_TOKEN_MISMATCH:
                            header('Location: /login', true, 302);
                            exit;
                        case AuthException::PROJECT_ACCESS_DENIED:
                        case AuthException::PROJECT_INSUFFICIENT_PERMISSIONS:
                        case AuthException::ALREADY_LOGGED_IN:
                            header('Location: /', true, 302);
                            exit;
                        default:
                            header('Location: /login', true, 302);
                            exit;
                    }
                }
        }
    }
}