<?php

namespace App\Services;

use App\Repositories\AuthRepository;

class AuthService
{
    private AuthRepository $authRepository;

    public function __construct($authRepository)
    {
        $this->authRepository = $authRepository;
    }

    //-----------------------------------------------------
    // Login & Logout methods -----------------------------
    //-----------------------------------------------------
    public function login($username, $password)
    {
        session_start();

        // TEMPORARY: Create user with hashed password in database for debugging
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $created = $this->authRepository->createUser($username, $hashedPassword);
        error_log("TEMP: Created user '$username' in database: " . ($created ? 'SUCCESS' : 'FAILED'));

        // Fetch user by username
        $user = $this->authRepository->getUserByUsername($username);

        // Uses password_verify's bcrypt
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
