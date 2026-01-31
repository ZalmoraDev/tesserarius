<?php

namespace App\Services\Interfaces;

use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;

interface UserServiceInterface
{
    /** Edit the logged-in user's account details
     * @throws ValidationException if validation fails
     */
    public function editAccount(string $newUsername, string $newEmail): void;

    /** Delete the logged-in user's account
     * @throws AuthException if deletion fails
     */
    public function deleteAccount(string $confirmName): void;
}