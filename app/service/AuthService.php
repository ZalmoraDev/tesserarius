<?php

namespace App\Service;

use App\Repository\AuthRepository;

class AuthService
{
    private AuthRepository $authRepository;

    public function __construct()
    {
        $this->authRepository = new AuthRepository();
    }

    //-----------------------------------------------------
    // Login & Logout methods -----------------------------
    //-----------------------------------------------------
    public function login($username, $password)
    {
        session_start();

        // Fetch user by username
        $user = $this->authRepository->getUserByUsername($username);

        if ($user && password_verify($password, $user->getPasswordHash())) {
            $_SESSION['userId'] = $user->getId();
            $_SESSION['username'] = $user->getUsername();
            return true;
        }
        return false;
    }


    public function logout(): void
    {
        // Clear session & redirect to login after logout
        session_start();
        session_unset();
        session_destroy();
        header("Location: /");
        exit();
    }

    //-----------------------------------------------------
    // Access control methods -----------------------------
    //-----------------------------------------------------
    public function checkIfLoggedIn(): void
    {
        session_start(); // Make sure session is initiated

        // Redirect to login page if not logged in
        if (!isset($_SESSION['userId'])) {
            header("Location: /");
            exit();
        }
    }

    public function checkLoginPageIfLoggedIn(): void
    {
        session_start(); // Make sure session is initiated

        // If the user is logged in, redirect to home
        if (isset($_SESSION['userId'])) {
            header("Location: /home");
            exit();
        }
    }


    public function shouldProjectBeAccessible($projectId): void
    {
        session_start(); // Make sure session is initiated

        if (!$this->authRepository->shouldProjectBeAccessible($_SESSION['userId'], $projectId)) {
            // Redirect to login page if not a member/admin of the project
            header("Location: /home?error=access_denied");
            exit();
        }
    }
}
