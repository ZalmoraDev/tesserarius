<?php

namespace App\Services;

use App\Models\User;
use App\Models\Enums\UserRole;
use App\Models\Enums\AccessRole;

use App\Repositories\AuthRepositoryInterface;

final class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepo;

    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    /** Verifies user credentials, returns User model if successful, null if not */
    public function authenticate(string $username, string $password): ?User
    {
        // TEMPORARY: Create user with hashed password in database for debugging
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $created = $this->authRepo->createUser($username, $hashedPassword, $username . "@temp.com");
        error_log("TEMP: Created user '$username' in database: " . ($created ? 'SUCCESS' : 'FAILED'));
        // TEMPORARY: END ------------------------------------------------------
        $user = $this->authRepo->getUserByUsername($username);

        if (!$user)
            return null;

        // Uses php's built-in BCrypt password hashing functions
        if (!password_verify($password, $user->passwordHash))
            return null;

        return $user;
    }

    /** Sets session data for logged-in user */
    public function login(User $user): void
    {
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'userId' => $user->id,
            'username' => $user->username,
            'ts' => time(),
        ];
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
}