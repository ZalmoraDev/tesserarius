<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;
use App\Services\Interfaces\AuthServiceInterface;
use Exception;

/** Controller handling user authentication & authorization */
final readonly class AuthController
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    //region GET Requests

    /** GET /login, acts as login page */
    public function loginPage(): void
    {
        View::render('login.php', "Login" . View::addSiteName());
    }

    /** GET /signup, serves signup page */
    public function signupPage(): void
    {
        View::render('signup.php', "Signup" . View::addSiteName());
    }
    //endregion


    //region POST Requests
    /** POST /auth/login, processes login form submission */
    public function login(): void
    {
        try {
            $this->authService->login(
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
            $_SESSION['flash_successes'][] = "You are now logged in.";
            $redirect = '/';
        } catch (AuthException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = '/login';
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = '/login';
        }
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /auth/signup, processes signup form submission */
    public function signup(): void
    {
        try {
            $username = $_POST['username'] ?? '';

            $this->authService->signup(
                $username,
                $_POST['email'] ?? '',
                $_POST['password'] ?? '',
                $_POST['password_confirm'] ?? ''
            );
            $_SESSION['flash_successes'][] = "Welcome " . $username . "! Your account has been created.";
            $redirect = '/';
        } catch (ValidationException $e) {
            $_SESSION['flash_errors'][] = $e->getMessage();
            $redirect = '/login';
        } catch (Exception) {
            $_SESSION['flash_errors'][] = "An unexpected error occurred.";
            $redirect = '/login';
        }
        header("Location: $redirect", true, 302);
        exit;
    }

    /** POST /auth/logout, serves logout action */
    public function logout(): void
    {
        $this->authService->logout();
        $_SESSION['flash_info'][] = "You have been logged out.";
        header("Location: /login", true, 302);
    }
    //endregion
}