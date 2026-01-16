<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;

interface UserRepositoryInterface
{
    public function findUserIdentityById(int $id): ?UserIdentityDto;
    public function findUserIdentityByEmail(string $email): ?UserIdentityDto;
    public function findUserIdentityByUsername(string $username): ?UserIdentityDto;
}