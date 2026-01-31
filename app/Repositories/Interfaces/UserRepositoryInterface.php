<?php

namespace App\Repositories\Interfaces;

use App\Dto\UserIdentityDto;

interface UserRepositoryInterface
{
    //region Retrieval
    /** Checks if a user with the given username already exists.
     * @return bool true if exists, false otherwise
     */
    public function existsByUsername(string $username): bool;

    /** Check if a user with the given email already exists.
     * @return bool true if exists, false otherwise
     */
    public function existsByEmail(string $email): bool;

    /** Retrieve a user by their id
     * @return UserIdentityDto|null
     */
    public function findUserIdentityById(int $id): ?UserIdentityDto;
    //endregion


    //region Modification
    /** Update a user's username and email by their id */
    public function updateUser(int $id, string $newUsername, string $newEmail): void;

    /** Delete user by their id
     * @return bool true if a row was deleted, false otherwise
     */
    public function deleteUser(int $id): bool;
    //endregion
}