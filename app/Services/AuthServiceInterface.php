<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enums\AccessRole;


interface AuthServiceInterface
{
    public function login(string $email, string $password): void;
    public function signup(string $username, string $email, string $password, string $passwordConfirm): void;

    public function logout(): void;

    public function isAuthenticated(): bool;

    public function isAccessAuthorized(int $projectId, AccessRole $requiredRole): bool;
}