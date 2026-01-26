<?php

namespace App\Routing;

use FastRoute;
use App\Models\Enums\AccessRole;

/** Separation of routes from router dispatching logic.
 * Defines all routes with their handlers and required access roles to be evaluated in router. */
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
            $project = $this->controllers['project'];
            $projectMembers = $this->controllers['projectMembers'];
            $user = $this->controllers['user'];

            // Uses route aliases instead of full $r->addRoute(METHOD, ...)
            // AuthController routes
            $r->get('/login', $this->route([$auth, 'loginPage'], AccessRole::Anyone));
            $r->get('/signup', $this->route([$auth, 'signupPage'], AccessRole::Anyone));

            $r->post('/auth/login', $this->route([$auth, 'login'], AccessRole::Anyone));
            $r->post('/auth/signup', $this->route([$auth, 'signup'], AccessRole::Anyone));
            $r->post('/auth/logout', $this->route([$auth, 'logout'], AccessRole::Anyone));

            // UserController routes (default for logged-in users '/')
            $r->get('/', $this->route([$user, 'homePage'], AccessRole::Authenticated));

            // ProjectController routes
            $r->get('/project/create', $this->route([$project, 'showCreate'], AccessRole::Authenticated));
            $r->post('/project/create', $this->route([$project, 'handleCreate'], AccessRole::Authenticated));
            $r->get('/project/view/{projectId:\d+}', $this->route([$project, 'showView'], AccessRole::Member));
            $r->get('/project/edit/{projectId:\d+}', $this->route([$project, 'showEdit'], AccessRole::Admin));
            $r->post('/project/edit/{projectId:\d+}', $this->route([$project, 'handleEdit'], AccessRole::Admin));
            $r->post('/project/delete/{projectId:\d+}', $this->route([$project, 'handleDeletion'], AccessRole::Owner));

            // ProjectMembersController routes
            $r->post('/project-members/join-project', $this->route([$projectMembers, 'handleJoinByInviteCode'], AccessRole::Authenticated));
            $r->post('/project-members/create-invites/{projectId:\d+}', $this->route([$projectMembers, 'handleInviteCreation'], AccessRole::Admin));
            $r->post('/project-members/remove-invite/{inviteId:\d+}', $this->route([$projectMembers, 'handleInviteDeletion'], AccessRole::Admin));

            $r->post('/project-members/promote/{projectId:\d+}/{memberId:\d+}', $this->route([$projectMembers, 'handleMemberPromote'], AccessRole::Owner));
            $r->post('/project-members/demote/{projectId:\d+}/{memberId:\d+}', $this->route([$projectMembers, 'handleMemberDemote'], AccessRole::Owner));
            $r->post('/project-members/remove/{projectId:\d+}/{memberId:\d+}', $this->route([$projectMembers, 'handleMemberRemoval'], AccessRole::Admin));
        });
    }

    /** Helper-object to create conciser route auth guard objects. */
    private function route(array $action, AccessRole $accessRole): array
    {
        return [
            'action' => $action,
            'accessRole' => $accessRole,
        ];
    }
}