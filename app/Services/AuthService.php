<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enums\AccessRole;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;
use App\Repositories\AuthRepositoryInterface;

final class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepo;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    // -------------------- Public Methods START --------------------

    /** Attempts to log in a user with provided credentials.
     * @throws AuthException if credentials are invalid. */
    public function login(string $email, string $password): void
    {
        $user = $this->authRepo->getUserByEmail($email);

        if ($user === null)
            throw new AuthException(AuthException::INVALID_CREDENTIALS);

        if (!password_verify($password, $user->passwordHash))
            throw new AuthException(AuthException::INVALID_CREDENTIALS);

        $this->setSessionAuthData($user);
    }

    /** Attempts to register a new user with provided data.
     * @throws ValidationException if any validation fails. */
    public function signup(string $username, string $email, string $password, string $passwordConfirm): void
    {
        $username = trim($username);
        $email = strtolower(trim($email));

        // required fields are empty
        if (empty($username) || empty($email) || empty($password))
            throw new ValidationException(ValidationException::FIELDS_REQUIRED);

        // password and confirmation do not match
        if ($password !== $passwordConfirm)
            throw new ValidationException(ValidationException::PASSWORD_MISMATCH);

        // username/email/password do not meet format requirements
        // Password regex: at least one lower, one upper, one digit, no spaces, length 12-64
        if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username))
            throw new ValidationException(ValidationException::USERNAME_INVALID);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new ValidationException(ValidationException::EMAIL_INVALID);
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$/', $password))
            throw new ValidationException(ValidationException::PASSWORD_INVALID);

        // username/email are already taken
        if ($this->authRepo->getUserByUsername($username))
            throw new ValidationException(ValidationException::USERNAME_TAKEN);
        if ($this->authRepo->getUserByEmail($email))
            throw new ValidationException(ValidationException::EMAIL_TAKEN);

        // failed attempt creating the new user
        $userId = $this->authRepo->createUser($username, $email, password_hash($password, PASSWORD_DEFAULT));
        if ($userId === null)
            throw new ValidationException(ValidationException::REGISTRATION_FAILED);

        // Upon successful creation, use their ID to fetch full user data.
        $user = $this->authRepo->getUserById($userId);
        if ($user === null)
            throw new ValidationException(ValidationException::REGISTRATION_FAILED);

        $this->setSessionAuthData($user);
    }

    /** Logs out by unsetting session auth data */
    public function logout(): void
    {
        // Only unset auth session data, regen session ID for CSRF protection.
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }

    /** Checks if the current user is authenticated (logged in) if the route requires it
     *
     * Used by Router.php
     * @throws AuthException if route requires authentication but user is not authenticated */
    public function requireAuthentication($routeReqRole): void
    {
        // AUTHENTICATION: If route requires authenticated user, but user is not authenticated, redirect to /login
        if ($routeReqRole >= AccessRole::Authenticated && !isset($_SESSION['auth']['userId']))
            throw new AuthException(AuthException::REQUIRES_LOGIN);
    }

    /** Checks if the currently authenticated user has access to the specified project with required role or higher
     *
     * Used by Router.php
     * @throws AuthException if user is not part of project or has insufficient permissions */
    public function requireProjectAccess(int $projectId, AccessRole $routeReqRole): void
    {
        // User is not part of this project
        $roleString = $this->authRepo->getUserProjectRole($_SESSION['auth']['userId'], $projectId);
        if ($roleString === null)
            throw new AuthException(AuthException::PROJECT_ACCESS_DENIED);

        // User's role in project is lower than required by route (member < admin < owner)
        $accessRole = AccessRole::from($roleString);
        if ($routeReqRole->value > $accessRole->value)
            throw new AuthException(AuthException::PROJECT_INSUFFICIENT_PERMISSIONS);
    }
    // -------------------- Public Methods END --------------------


    // -------------------- Private Methods START --------------------

    /** Sets session auth data for logged in or newly registered user */
    private function setSessionAuthData(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['auth'] = [
            'userId' => $user->id,
            'userEmail' => $user->email,
            'username' => $user->username,
            'ts' => time() // Currently not used for session expiration
        ];
    }

    // -------------------- Private Methods END --------------------
}