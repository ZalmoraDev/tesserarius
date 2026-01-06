<?php

namespace App;

use FastRoute;

class Router
{
    private array $c;

    public function __construct(array $constrollers)
    {
        $this->c = $constrollers;
    }

    /// @nikic/fast-route | https://packagist.org/packages/nikic/fast-route
    /// Uses basic usage implementation from documentation, with changes to correctly calling class methods as handlers
    public function dispatch(): void
    {

        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            // homepage for logged in users, default to URL '/'
            $r->addRoute('GET', '/', [$this->c['home'], 'index']);

            $r->addRoute('POST', '/auth/login', [$this->c['auth'], 'login']);
            $r->addRoute('GET', '/auth/logout', [$this->c['auth'], 'logout']);
            $r->addRoute('GET', '/auth/signup', [$this->c['auth'], 'signup']);

            $r->addRoute('GET', '/login', [$this->c['login'], 'index']);

            $r->addRoute('GET', '/project/{id:\d+}', [$this->c['auth'], 'view']);
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

                // ... call $handler with $vars
                // METHOD + URI → handler + params
                call_user_func_array($handler, $vars);
                break;
        }
    }
}