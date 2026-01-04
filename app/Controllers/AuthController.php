<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        // On POST-Request -> Try matching login credentials
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->authService->login($username, $password)) {
                // Successful login -> Redirect to /home
                header("Location: /home", true, 302);
                exit();
            } else {
                // Login failed -> Redirect back to /login (/) with error message
                // /login URL is not relevant, but it's needed for the redirect to work
                header("Location: /login?error=invalid_credentials", true, 302);
                exit();
            }
        } else {
            // On GET-Request -> Go back to login page
            header("Location: /login?error=direct_url_access", true, 302);
            exit();
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
    }
}
