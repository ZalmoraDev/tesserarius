<?php

namespace App;

use App\Models\Enums\AccessRole;
use App\Services\AuthService;
use App\Repositories\AuthRepository;

use App\Middleware\CsrfService;
use FastRoute;

final class Router
{
    // Abbreviated for route verbosity
    private array $controllers;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /** nikic/fast-route | https://packagist.org/packages/nikic/fast-route.
     * Modification on the basic usage implementation from docs, contains additional header data regarding route Access rights. */
    public function dispatch(): void
    {
        // Uses route aliases instead of full $r->addRoute(METHOD, ...)
        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            // Retrieve controllers and use them as conciser abbreviations in route handler definitions
            $auth = $this->controllers['auth'];
            $home = $this->controllers['home'];
            $project = $this->controllers['project'];

            $r->get('/login', $this->route([$auth, 'loginPage'], AccessRole::Anyone));
            $r->get('/signup', $this->route([$auth, 'signupPage'], AccessRole::Anyone));
            $r->post('/auth/login', $this->route([$auth, 'loginAuth'], AccessRole::Anyone));
            $r->post('/auth/signup', $this->route([$auth, 'signupAuth'], AccessRole::Anyone));
            $r->post('/auth/logout', $this->route([$auth, 'logout'], AccessRole::Anyone));

            // default page for logged-in users, default to URL '/'
            $r->get('/', $this->route([$home, 'index'], AccessRole::Authenticated));

            $r->get('/project/{projectId:\d+}', $this->route([$project, 'view'], AccessRole::Member));
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

                // Abbreviate required access role for this route AND retrieve auth service
                $required = $handler['accessRole'];
                $authService = $this->controllers['auth']->getAuthService();

                // AUTHENTICATION: If route requires authenticated user, but user is not authenticated, redirect to /login
                if ($required >= AccessRole::Authenticated &&
                    $authService->isAuthenticated() === false) {
                    header('Location: /login?error=requires_login', true, 302);
                    exit;
                }

                // AUTHORIZATION: If route requires higher role then is accessing, redirect to / (homepage)
                if ($required >= AccessRole::Member &&
                    $authService->isAccessAuthorized($vars['projectId']) === false) {
                    header('Location: /?error=you_are_not_authorized_to_access_this_page', true, 403);
                    exit;
                }

                // Upon POST requests, verify CSRF token. If not valid, exit with 403
                $csrfService = new CsrfService();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $csrfService->verify($_POST['csrf'] ?? null);
                }

                // (If no authentication was required OR user passed auth guards) & CSRF token is validated -> call handler
                // METHOD + URI → handler + params
                call_user_func_array($handler['handler'], $vars);
                break;
        }
    }

    /** Helper-object to create more concise route auth guard objects. */
    private function route(array $handler, AccessRole $minRole): array
    {
        return [
            'handler' => $handler,
            'accessRole' => $minRole
        ];
    }

}