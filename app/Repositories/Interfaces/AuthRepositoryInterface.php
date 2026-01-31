<?php

namespace App\Repositories\Interfaces;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\UserRole;

interface AuthRepositoryInterface
{
    /** Retrieve a user by their email, returns User model or null if not found
     * @return UserAuthDto|null User authentication data or null if not found
     */
    public function findAuthByEmail(string $email): ?UserAuthDto;

    /** Retrieve the role of a user in a specific project, for router access control
     * @return UserRole|null User's role in given project, or null if not a member
     */
    public function findUserProjectRole(int $projectId, int $userId): ?UserRole;

    /** Create a new user in the database, after the service has validated the data
     * @return UserIdentityDto|null Created user's identity data, or null on failure
     */
    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto;
}