<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ServiceException;
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

        // Fetch current user identity and check for changes
        $identity = ServiceException::handleRepoCall(
            fn() => $this->userRepo->findUserIdentityById((int)$_SESSION['auth']['userId']),
            ValidationException::class,
            __FUNCTION__
        );
        if ($identity === null)
            throw new ValidationException(ValidationException::USER_NOT_FOUND);
        if ($identity->username === $newUsername && $identity->email === $newEmail)
            return;

        // If a new username is provided, validate it
        if ($newUsername !== $identity->username) {
            if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $newUsername))
                throw new ValidationException(ValidationException::USERNAME_INVALID);

            $usernameExists = ServiceException::handleRepoCall(
                fn() => $this->userRepo->existsByUsername($newUsername),
                ValidationException::class,
                __FUNCTION__
            );
            if ($usernameExists)
                throw new ValidationException(ValidationException::USERNAME_TAKEN);
        }

        // If a new email is provided, validate it
        if ($newEmail !== $identity->email) {
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
                throw new ValidationException(ValidationException::EMAIL_INVALID);

            $emailExists = ServiceException::handleRepoCall(
                fn() => $this->userRepo->existsByEmail($newEmail),
                ValidationException::class,
                __FUNCTION__
            );
            if ($emailExists)
                throw new ValidationException(ValidationException::EMAIL_TAKEN);
        }

        // If one field fails, no changes are made to either since update is called once here
        ServiceException::handleRepoCall(
            fn() => $this->userRepo->updateUser($identity->id, $newUsername, $newEmail),
            ValidationException::class,
            __FUNCTION__
        );

        // Update session data
        $_SESSION['auth']['username'] = $newUsername;
        $_SESSION['auth']['userEmail'] = $newEmail;
    }

    public function deleteAccount(string $confirmName): void
    {
        $username = $_SESSION['auth']['username'];

        if (!isset($confirmName))
            throw new AuthException(AuthException::DELETION_REQUIRES_CONFIRMATION);
        if ($confirmName !== $username)
            throw new AuthException(AuthException::DELETION_NAME_MISMATCH);

        // Delete the user
        ServiceException::handleRepoCall(
            fn() => $this->userRepo->deleteUser($_SESSION['auth']['userId']),
            AuthException::class,
            __FUNCTION__
        );

        // Clear session data
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }
}