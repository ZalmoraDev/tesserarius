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

    /// POST
    public function login(): void
    {
        // On POST-Request -> Try matching login credentials
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($username, $password)) {
                // Successful login -> Redirect to home '/'
                header("Location: /", true, 302);
            } else {
                // Login failed -> Redirect back to /login (/) with error message
                // /login URL is not relevant, but it's needed for the redirect to work
                header("Location: /login?error=invalid_credentials", true, 302);
            }
            exit();
        } else {
            // On GET-Request -> Go back to login page
            header("Location: /login?error=direct_url_access", true, 302);
            exit();
        }
    }

    public function signup(): void
    {
        global $title, $view;
        $title = "Home | Tesserarius";
        $view = __DIR__ . '/../Views/signup.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }

    public function logout(): void
    {
        global $title, $view;
        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';

        $this->authService->logout();
    }
}
