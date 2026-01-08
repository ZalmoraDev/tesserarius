<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    /// GET, acts as login page
    public function index()
    {
        global $title, $view;

        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    /// POST
    public function login(): void
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

    /// GET
    public function signup(): void
    {
        global $title, $view;
        $title = "Home | Tesserarius";
        $view = __DIR__ . '/../Views/signup.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    /// GET
    public function logout(): void
    {
        global $title, $view;
        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $this->authService->logout();
        header("Location: /login");
    }
}
