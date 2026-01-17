<?php

namespace App\Repositories;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\AccessRole;
use App\Models\Enums\UserRole;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto;
    public function findAuthByEmail(string $email): ?UserAuthDto;
    public function findUserProjectRole(int $projectId, int $userId): ?UserRole;
}