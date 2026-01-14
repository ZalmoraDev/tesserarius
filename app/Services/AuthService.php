<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Models\Enums\AccessRole;

use App\Repositories\AuthRepositoryInterface;

use App\Exceptions\AuthException;

final class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepo;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    // -------------------- Public Methods START --------------------

    /** Attempts to log in a user with provided email and password. */
    public function login(string $email, string $password): void
    {
        $user = $this->authRepo->getUserByEmail($email);

        if ($user === null)
            throw new AuthException(AuthException::INVALID_CREDENTIALS);

        if (!password_verify($password, $user->passwordHash))
            throw new AuthException(AuthException::INVALID_CREDENTIALS);

        $this->setSessionAuthData($user);
    }

    /** Attempt to register a new user with provided username, email and password. */
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

        // First attempt to create the user
        $userId = $this->authRepo->createUser($username, $email, password_hash($password, PASSWORD_DEFAULT));
        if ($userId === null)
            throw new AuthException(AuthException::REGISTRATION_FAILED);

        // Upon successful creation, use their ID to fetch full user data.
        $user = $this->authRepo->getUserById($userId);
        if ($user === null)
            throw new AuthException(AuthException::REGISTRATION_FAILED);

        $this->setSessionAuthData($user);
    }

    /** Logs out by unsetting session auth data */
    public function logout(): void
    {
        // Only unset auth session data, regen session ID for CSRF protection.
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }

    /** Checks if a user is currently authenticated (logged in)
     *
     * Used by Router.php */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['auth']['userId']);
    }

    /** Checks if the currently authenticated user meets the minimum
     * access role required for a given project.
     *
     * Enum comparison is done via their integer values.
     * The higher the enum-value, the more privileged the role,
     * Member=1 < Admin=2 < Owner=3
     *
     * Used by Router.php*/
    public function isAccessAuthorized(int $projectId, AccessRole $requiredRole): bool
    {
        $roleString = $this->authRepo->getUserProjectRole($_SESSION['auth']['userId'], $projectId);

        // Should not happen, means user has no role in this project
        if ($roleString === null)
            return false;

        // Convert role string to UserRole enum, which gets converted into it's int value for comparison.
        $userRole = UserRole::from($roleString);
        return $userRole->value >= $requiredRole->value;
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