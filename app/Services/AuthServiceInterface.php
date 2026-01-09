<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enums\AccessRole;


interface AuthServiceInterface
{
    public function authenticate(string $username, string $password): ?User;

    public function login(User $user): void;

    public function logout(): void;

    public function isAuthenticated(): bool;

    public function isAccessAuthorized(int $projectId, AccessRole $requiredRole): bool;
}