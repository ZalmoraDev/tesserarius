<?php

namespace App\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $passwordHash, string $email): bool;

    public function getUserByUsername(string $username): ?User;

    public function getUserProjectRole(int $projectId, int $userId): ?string;
}
