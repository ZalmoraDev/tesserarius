<?php

namespace App\Repositories\Interfaces;

use App\Dto\UserIdentityDto;
use App\Repositories\Exceptions\UserRepoException;

interface UserRepositoryInterface
{
    //region Retrieval
    /** Checks if a user with the given username already exists.
     * @return bool true if exists, false otherwise
     * @throws UserRepoException if database query fails
     */
    public function existsByUsername(string $username): bool;

    /** Check if a user with the given email already exists.
     * @return bool true if exists, false otherwise
     * @throws UserRepoException if database query fails
     */
    public function existsByEmail(string $email): bool;

    /** Retrieve a user by their id
     * @return UserIdentityDto|null
     * @throws UserRepoException if database query fails
     */
    public function findUserIdentityById(int $id): ?UserIdentityDto;
    //endregion


    //region Modification
    /** Update a user's username and email by their id
     * @throws UserRepoException if user not found or database operation fails
     */
    public function updateUser(int $id, string $newUsername, string $newEmail): void;

    /** Delete user by their id
     * @throws UserRepoException if user not found or database operation fails
     */
    public function deleteUser(int $id): void;
    //endregion
}