<?php

namespace App\Services;

use App\Dto\UserIdentityDto;
use App\Models\Enums\AccessRole;
use App\Repositories\UserRepositoryInterface;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;
use App\Repositories\AuthRepositoryInterface;

final class AuthService implements AuthServiceInterface
{
    private AuthRepositoryInterface $authRepo;
    private UserRepositoryInterface $userRepo;

    public function __construct(AuthRepositoryInterface $authRepo, UserRepositoryInterface $userRepo)
    {
        $this->authRepo = $authRepo;
        $this->userRepo = $userRepo;
    }

    // -------------------- Public Methods START --------------------

    /** Attempts to log in a user with provided credentials.
     * @throws AuthException if credentials are invalid.
     */
    public function login(string $email, string $password): void
    {
        $auth = $this->authRepo->findAuthByEmail($email);
        if ($auth === null)
            throw new AuthException(AuthException::INVALID_CREDENTIALS);
        if (!password_verify($password, $auth->passwordHash))
            throw new AuthException(AuthException::INVALID_CREDENTIALS);

        $identity = $this->userRepo->findUserIdentityById($auth->id);
        $this->setSessionData($identity);
    }


    /** Logs out by unsetting session auth data */
    public function logout(): void
    {
        // Only unset auth session data, regen session ID for CSRF protection.
        unset($_SESSION['auth']);
        session_regenerate_id(true);
    }


    /** Attempts to register a new user with provided data.
     * @throws ValidationException if any validation fails.
     */
    public function signup(string $username, string $email, string $password, string $passwordConfirm): void
    {
        $username = trim($username);
        $email = strtolower(trim($email));

        // required fields are empty
        if (empty($username) || empty($email) || empty($password))
            throw new ValidationException(ValidationException::FIELDS_REQUIRED);

        // password and confirmation do not match
        if ($password !== $passwordConfirm)
            throw new ValidationException(ValidationException::PASSWORD_MISMATCH);

        // username/email/password do not meet format requirements
        // Password regex: at least one lower, one upper, one digit, no spaces, length 12-64
        if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username))
            throw new ValidationException(ValidationException::USERNAME_INVALID);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new ValidationException(ValidationException::EMAIL_INVALID);
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])\S{12,64}$/', $password))
            throw new ValidationException(ValidationException::PASSWORD_INVALID);

        // username/email are already taken
        if ($this->userRepo->findUserIdentityByUsername($username))
            throw new ValidationException(ValidationException::USERNAME_TAKEN);
        if ($this->userRepo->findUserIdentityByEmail($email))
            throw new ValidationException(ValidationException::EMAIL_TAKEN);

        // failed attempt creating the new user
        $identity = $this->authRepo->createUser($username, $email, password_hash($password, PASSWORD_DEFAULT));
        if ($identity === null)
            throw new ValidationException(ValidationException::REGISTRATION_FAILED);

        $this->setSessionData($identity);
    }


    /** Checks if the current user is authenticated (logged in) if the route requires it
     * @throws AuthException if route requires authentication but user is not authenticated
     */
    public function requireAuthentication($routeReqRole): void
    {
        // AUTHENTICATION: If route requires authenticated user, but user is not authenticated, redirect to /login
        if ($routeReqRole >= AccessRole::Authenticated && !isset($_SESSION['auth']['userId']) )
            throw new AuthException(AuthException::REQUIRES_LOGIN);
    }


    /** Checks if the currently authenticated user has access to the specified project with required role or higher
     * @throws AuthException if user is not part of project or has insufficient permissions
     */
    public function requireProjectAccess(int $projectId, AccessRole $routeReqAccess): void
    {
        if (!isset($_SESSION['auth']))
            throw new AuthException(AuthException::PROJECT_ACCESS_DENIED);

        $userRole = $this->authRepo->findUserProjectRole($projectId, (int)$_SESSION['auth']['userId']);

        if ($userRole === null)
            throw new AuthException(AuthException::PROJECT_ACCESS_DENIED);

        // Convert UserRole to AccessRole logic for comparison
        if ($routeReqAccess->value > $userRole->toAccessRole()->value)
            throw new AuthException(AuthException::PROJECT_INSUFFICIENT_PERMISSIONS);
    }


    /** Checks if user is already logged in when accessing login/signup pages
     * @throws AuthException if user is already logged in and tries to access login/signup pages
     */
    public function denyAuthenticatedOnAuthRoutes(string $routeName): void
    {
        if (($routeName === 'loginPage' || $routeName === 'signupPage') && isset($_SESSION['auth']['userId']))
            throw new AuthException(AuthException::ALREADY_LOGGED_IN);
    }

    // -------------------- Public Methods END --------------------


    // -------------------- Private Methods START --------------------

    /** Sets session auth data for logged in or newly registered user */
    private function setSessionData(UserIdentityDto $user): void
    {
        session_regenerate_id(true);
        $_SESSION['auth'] = [
            'userId' => $user->id,
            'userEmail' => $user->email,
            'username' => $user->username,
            'ts' => time() // Currently not used for session expiration
        ];
    }

    // -------------------- Private Methods END --------------------
}