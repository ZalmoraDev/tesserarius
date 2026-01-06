<?php

namespace App\Services;

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

    public function shouldProjectBeAccessible($projectId): void
    {
        if (!$this->authRepository->shouldProjectBeAccessible($_SESSION['userId'], $projectId)) {
            // Redirect to login page if not a member/admin of the project
            // TODO: header redirects should be handled in controllers, not services
            header("Location: /login?error=access_denied");
        }
    }
}
