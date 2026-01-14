<?php

namespace App\Repositories;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $email, string $passwordHash): ?int;

    public function getUserByEmail(string $email): ?User;
    public function getUserByUsername(string $username): ?User;

    // TODO: Change to ?User return type?
    public function getUserProjectRole(int $projectId, int $userId): ?string;
}
