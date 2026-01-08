<?php

namespace App\Services;

use App\Models\Enums\UserRole;
use App\Models\Enums\AccessRole;
use App\Models\User;
use App\Repositories\AuthRepository;

final class AuthService
{
    private AuthRepository $authRepository;

    public function __construct($authRepository)
    {
        $this->authRepository = $authRepository;
    }

    //-----------------------------------------------------
    // Login & Logout methods -----------------------------
    //-----------------------------------------------------

    /// Attempt to log fetch user model by username and verify password
    public function authenticate($username, $password): ?User
    {
        // TEMPORARY: Create user with hashed password in database for debugging
//        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//        $created = $this->authRepository->createUser($username, $hashedPassword);
//        error_log("TEMP: Created user '$username' in database: " . ($created ? 'SUCCESS' : 'FAILED'));
        // TEMPORARY: END ------------------------------------------------------
        $user = $this->authRepository->getUserByUsername($username);

        if (!$user)
            return null;

        // Uses php's built-in BCrypt password hashing functions
        if (!password_verify($password, $user->getPasswordHash()))
            return null;

        return $user;
    }

    /// Use User model to set session data for logged-in user
    public function login(User $user): void
    {
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'userId' => $user->getId(),
            'username' => $user->getUsername(),
            'ts' => time(),
        ];
    }

    public function logout(): void
    {
        // Only unset auth session data, regen session ID for csrf protection
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION['auth']['userId']);
    }

    /** Checks if the currently authenticated user meets the minimum
     * access role required for a given project.
     *
     * Enum comparison is done via their integer values.
     * The higher the enum-value, the more privileges the role has,
     * Member < Admin < Owner */
    public function isAccessAuthorized(int $projectId, AccessRole $requiredRole): bool
    {
        $roleString = $this->authRepository->getUserProjectRole($_SESSION['auth']['userId'], $projectId);

        // Should not happen, means user has no role in this project
        if ($roleString === null) {
            return false;
        }

        // Convert role string to UserRole enum, which gets converted into it's int value for comparison.
        $userRole = UserRole::from($roleString);
        return $userRole->value >= $requiredRole->value;
    }
}
