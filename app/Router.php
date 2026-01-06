<?php

namespace App;

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
            // default page for logged-in users, default to URL '/'
            $r->get('/', $this->routeObj([$this->c['home'], 'index']));

            $r->get('/login', $this->routeObj([$this->c['login'], 'index'], false));

            $r->get('/auth/signup', $this->routeObj([$this->c['auth'], 'signup'], false));
            $r->post('/auth/login', $this->routeObj([$this->c['auth'], 'login'], false));
            $r->post('/auth/logout', $this->routeObj([$this->c['auth'], 'logout'], false));

            $r->get('/project/{id:\d+}', $this->routeObj([$this->c['auth'], 'view']));
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

                // auth guard
                // If route requires authentication, but user isn't authenticated -> redirect to /login
                // TODO: Pass this AuthService object more gracefully regarding dependency injection fomr index.php
                $authService = new AuthService(new AuthRepository());
                if (($handler['auth'] ?? false) && $authService->isAuthenticated()) {
                    // TODO: Find better way of handling error back to user
                    header('Location: /login?error=requires_login', true, 302);
                    exit;
                }

                // call $handler with $vars
                // METHOD + URI → handler + params
                call_user_func_array($handler['handler'], $vars);
                break;
        }
    }

    /// Helper to create route definition objects
    /// Also makes unintended page unauthorization less likely by the default: $auth = true
    private function routeObj(array $handler, bool $auth = true): array
    {
        return ['handler' => $handler, 'auth' => $auth];
    }

}