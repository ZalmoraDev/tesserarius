<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;

interface UserRepositoryInterface
{
    public function findUserIdentityById(int $id): ?UserIdentityDto;
    public function existsByUsername(string $username): bool;
    public function existsByEmail(string $email): bool;
    public function updateUser(int $id, string $newUsername, string $newEmail): void;
}