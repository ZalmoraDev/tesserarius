<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;

class LoginController
{

    public function __construct()
    {
        $authService = new AuthService();
        $authService->checkLoginPageIfLoggedIn(); // If not logged in, redirect to login page
    }

    public function index()
    {
        global $title, $view;

        $title = "Login | Tesserarius";
        $view = __DIR__ . '/../Views/login.php';
        require __DIR__ . '/../Views/skeleton/base.php';
    }
}
