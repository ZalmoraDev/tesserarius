<?php

namespace App\Controllers;

use App\Services\AuthService;

final class AuthController
{
    private AuthService $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    /** Used by router to authenticate and authorize access to routes */
    public function getAuthService(): AuthService
    {
        return $this->authService;
    }

    // -------------------- GET Actions --------------------
    /** GET, acts as login page */
    public function loginPage()
    {
        global $title, $view;

        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }


    /** GET, serves signup page */
    public function signupPage(): void
    {
        global $title, $view;
        $title = "Signup | Tesserarius";
        $view = __DIR__ . '/../Views/signup.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    // -------------------- POST Actions --------------------
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
            //$this->redirect('/login?error=invalid_credentials');
            header("Location: /login?error=invalid_credentials", true, 302);
        }

        // Sets session data for logged-in user
        $this->authService->login($user);
        header("Location: /", true, 302);
        //$this->redirect('/');
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