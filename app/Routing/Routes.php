<?php

namespace App\Routing;

use FastRoute;
use App\Models\Enums\AccessRole;

/** Separation of routes from router dispatching logic.
 * Defines all routes with their handlers and required access roles to be evalualted in router. */
final class Routes
{
    private array $controllers;

    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /** nikic/fast-route | https://packagist.org/packages/nikic/fast-route.
     * Route definitions with additional access rights to be evaluated by router. */
    public function dispatcher(): FastRoute\Dispatcher
    {
        return FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            // Retrieve controllers and use them as conciser abbreviations in route handler definitions
            $auth = $this->controllers['auth'];
            $user = $this->controllers['user'];
            $project = $this->controllers['project'];

            // Uses route aliases instead of full $r->addRoute(METHOD, ...)
            // AuthController routes
            $r->get('/login', $this->route([$auth, 'loginPage'], AccessRole::Anyone));
            $r->get('/signup', $this->route([$auth, 'signupPage'], AccessRole::Anyone));
            $r->post('/auth/login', $this->route([$auth, 'login'], AccessRole::Anyone));
            $r->post('/auth/signup', $this->route([$auth, 'signup'], AccessRole::Anyone));
            $r->post('/auth/logout', $this->route([$auth, 'logout'], AccessRole::Anyone));

            // default page for logged-in users, default to URL '/'
            // UserController routes
            $r->get('/', $this->route([$user, 'homePage'], AccessRole::Authenticated));

            // ProjectController routes
            $r->get('/project/create', $this->route([$project, 'showCreate'], AccessRole::Authenticated));
            $r->post('/project/create', $this->route([$project, 'handleCreate'], AccessRole::Authenticated));
            $r->get('/project/{projectId:\d+}', $this->route([$project, 'showPage'], AccessRole::Member));
        });
    }

    /** Helper-object to create conciser route auth guard objects. */
    private function route(array $handler, AccessRole $accessRole): array
    {
        return [
            'handler' => $handler,
            'accessRole' => $accessRole,
        ];
    }
}