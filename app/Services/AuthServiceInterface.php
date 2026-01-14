<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enums\AccessRole;


interface AuthServiceInterface
{
    public function login(string $email, string $password): void;
    public function signup(string $username, string $email, string $password, string $passwordConfirm): void;

    public function logout(): void;

    public function requireAuthentication(AccessRole $routeReqRole): void;

    public function requireProjectAccess(int $projectId, AccessRole $routeReqRole): void;
}