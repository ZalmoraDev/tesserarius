<?php

namespace App\Repositories\Interfaces;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\UserRole;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto;

    public function findAuthByEmail(string $email): ?UserAuthDto;

    public function findUserProjectRole(int $projectId, int $userId): ?UserRole;
}