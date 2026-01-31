<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;
use App\Services\Interfaces\UserServiceInterface;

final readonly class UserService implements UserServiceInterface
{
    private UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function editAccount(string $newUsername, string $newEmail): void
    {
        if (empty($newUsername) || empty($newEmail))
            throw new ValidationException(ValidationException::FIELDS_REQUIRED);

        $newUsername = trim($newUsername);
        $newEmail = strtolower(trim($newEmail));

        $identity = $this->userRepo->findUserIdentityById((int)$_SESSION['auth']['userId']);

        // If user is not logged in / no fields changed
        if ($identity === null)
            throw new ValidationException(ValidationException::USER_NOT_FOUND);
        if ($identity->username === $newUsername && $identity->email === $newEmail)
            return;

        // Username & email use same validation logic as signup(...)
        // If a new username is provided, validate it
        if ($newUsername !== $identity->username) {
            if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $newUsername))
                throw new ValidationException(ValidationException::USERNAME_INVALID);
            if ($this->userRepo->existsByUsername($newUsername))
                throw new ValidationException(ValidationException::USERNAME_TAKEN);
        }

        // If a new email is provided, validate it
        if ($newEmail !== $identity->email) {
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
                throw new ValidationException(ValidationException::EMAIL_INVALID);
            if ($this->userRepo->existsByEmail($newEmail))
                throw new ValidationException(ValidationException::EMAIL_TAKEN);
        }

        // If one field fails, no changes are made to either field
        $_SESSION['auth']['username'] = $newUsername;
        $_SESSION['auth']['userEmail'] = $newEmail;
        $this->userRepo->updateUser($identity->id, $newUsername, $newEmail);
    }

    public function deleteAccount(string $confirmName): void
    {
        $username = $_SESSION['auth']['username'];

        if (!isset($confirmName))
            throw new AuthException(AuthException::DELETION_REQUIRES_CONFIRMATION);
        if ($confirmName !== $username)
            throw new AuthException(AuthException::DELETION_NAME_MISMATCH);

        // failed attempt deleting the project
        $success = $this->userRepo->deleteUser($_SESSION['auth']['userId']);
        if (!$success)
            throw new AuthException(AuthException::DELETION_FAILED);

        // Clear session data
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }
}