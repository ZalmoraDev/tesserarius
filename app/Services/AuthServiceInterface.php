<?php

namespace App\Services;

use App\Models\Enums\AccessRole;
use App\Models\Enums\UserRole;


interface AuthServiceInterface
{
    // AuthController methods
    public function login(string $email, string $password): void;

    public function logout(): void;

    public function signup(string $username, string $email, string $password, string $passwordConfirm): void;

    // Router methods
    public function requireAuthentication(AccessRole $routeReqRole): void;

    public function requireProjectAccess(int $projectId, AccessRole $routeReqAccess): UserRole;

    public function denyAuthenticatedOnAuthRoutes(string $routeName): void;
}