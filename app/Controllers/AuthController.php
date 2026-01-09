<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\AuthServiceInterface;

final class AuthController
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /** Getter for AuthService, only used in Router to check authentication and authorization */
    public function getAuthService(): AuthServiceInterface
    {
        return $this->authService;
    }

    // -------------------- GET Requests --------------------

    /** GET, acts as login page */
    public function loginPage(): void
    {
        View::render('login.php', "Login" . View::getSiteName());
    }

    /** GET, serves signup page */
    public function signupPage(): void
    {
        View::render('signup.php', "Signup" . View::getSiteName());
    }

    // -------------------- POST Requests --------------------

    /** POST, processes login form submission */
    public function loginAuth(): void
    {
        // Attempts to authenticate user with provided credentials
        $user = $this->authService->authenticate(
            $_POST['username'] ?? '',
            $_POST['password'] ?? ''
        );

        // Authentication failed -> Redirect back to /login with error message
        if (!$user) {
            header("Location: /login?error=invalid_credentials", true, 302);
        }

        // Sets session data for logged-in user
        $this->authService->login($user);
        header("Location: /", true, 302);
    }

    /** POST, processes signup form submission */
    public function signupAuth(): void
    {
        // TODO: Implement signupAuth method

        // After successful signup, redirect to login page
        $this->loginAuth();
    }

    /** POST, serves logout action */
    public function logout(): void
    {
        $this->authService->logout();
        header("Location: /login", true, 302);
    }
}