<?php

namespace App\Controllers;

use App\Services\AuthService;

class LoginController
{
    private AuthService $authService;

    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        $this->authService->checkLoginPageIfLoggedIn(); // If not logged in, redirect to login page

        global $title, $view;

        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}
