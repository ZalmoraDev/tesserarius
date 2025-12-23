<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\UserService;

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
        $view = __DIR__ . '/../view/Login.php';
        require __DIR__ . '/../view/skeleton/Base.php';
    }
}
