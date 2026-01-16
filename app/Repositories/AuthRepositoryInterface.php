<?php

namespace App\Repositories;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\AccessRole;

interface AuthRepositoryInterface
{
    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto;
    public function findAuthByEmail(string $email): ?UserAuthDto;
    public function findUserAccessRole(int $projectId, int $userId): ?AccessRole;
}