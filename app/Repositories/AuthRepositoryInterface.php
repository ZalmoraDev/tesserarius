<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $email, string $passwordHash): ?int;

    public function getUserIdentityByEmail(string $email): ?UserIdentityDto;
    public function getUserIdentityByUsername(string $username): ?UserIdentityDto;

    // TODO: Change to ?User return type?
    public function getUserProjectRole(int $projectId, int $userId): ?string;
}
