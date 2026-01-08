<?php

namespace App;

use App\Models\Enums\AccessRole;
use App\Models\Enums\UserRole;

use App\Repositories\AuthRepository;
use App\Services\AuthService;
use FastRoute;
use App\Middleware\CsrfService;

final class Router
{
    // Abbreviated for route verbosity
    private array $c;

    public function __construct(array $controllers)
    {
        $this->c = $controllers;
    }

    /// @nikic/fast-route | https://packagist.org/packages/nikic/fast-route
    /// Uses basic usage implementation from documentation, with changes to correctly calling class methods as handlers
    public function dispatch(): void
    {
        // Upon POST requests, verify CSRF token. If not valid, exit with 403
        $csrfService = new CsrfService();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfService->verify($_POST['csrf'] ?? null);
        }

        // Uses alias for route definitions instead of $r->addRoute(METHOD, ...)
        // Handler contains added optional auth boolean, evaluated when dispatching below
        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->get('/login', $this->routeObj([$this->c['auth'], 'index'], AccessRole::Anyone));
            $r->get('/auth/signup', $this->routeObj([$this->c['auth'], 'signup'], AccessRole::Anyone));
            $r->post('/auth/login', $this->routeObj([$this->c['auth'], 'login'], AccessRole::Anyone));
            $r->post('/auth/logout', $this->routeObj([$this->c['auth'], 'logout'], AccessRole::Anyone));

            // default page for logged-in users, default to URL '/'
            $r->get('/', $this->routeObj([$this->c['home'], 'index'], AccessRole::Authenticated));

            $r->get('/project/{projectId:\d+}', $this->routeObj([$this->c['auth'], 'view'], AccessRole::Member));
        });

        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

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

                // TODO: Pass this AuthService object more gracefully regarding dependency injection from index.php
                $authService = new AuthService(new AuthRepository());

                $required = $handler['accessRole'];

                // AUTHENTICATION: If route requires authenticated user, but user is not authenticated, redirect to /login
                if ($required >= AccessRole::Authenticated &&
                    $authService->isAuthenticated() === false) {
                    header('Location: /login?error=requires_login', true, 302);
                    exit;
                }

                // TODO: If any other type of authorization besides $vars['$projectId'] is needed, create more guards here

                // AUTHORIZATION: If route requires higher role then is accessing, redirect to / (homepage)
                if ($required >= AccessRole::Member &&
                    $authService->isAccessAuthorized($vars['projectId']) === false) {
                    header('Location: /?error=you_are_not_authorized_to_access_this_page', true, 403);
                    exit;
                }

                // If no authentication was required, or user passed auth guards, call handler
                // METHOD + URI → handler + params
                call_user_func_array($handler['handler'], $vars);
                break;
        }
    }

/// Helper-object to create route auth guard definition objects.
    private
    function routeObj(array $handler, AccessRole $minRole): array
    {
        return [
            'handler' => $handler,
            'accessRole' => $minRole
        ];
    }

}