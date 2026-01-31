<?php

namespace App\Services\Interfaces;

use App\Models\Enums\AccessRole;
use App\Models\Enums\UserRole;
use App\Services\Exceptions\AuthException;
use App\Services\Exceptions\ValidationException;


interface AuthServiceInterface
{
    //region Auth
    /** Attempts to log in a user with provided credentials.
     * @throws AuthException if credentials are invalid.
     */
    public function login(string $email, string $password): void;

    /** Logs out by unsetting session auth data */
    public function logout(): void;

    /** Attempts to register a new user with provided data.
     * @throws ValidationException if any validation fails.
     */
    public function signup(string $username, string $email, string $password, string $passwordConfirm): void;
    //endregion


    //region Router
    /** Checks if the current user is authenticated (logged in) if the route requires it
     * @throws AuthException if route requires authentication but user is not authenticated
     */
    public function requireAuthentication(AccessRole $routeReqRole): void;

    /** Checks if the currently authenticated user has access to the specified project with required role or higher
     * @return UserRole The user's role in the project
     * @throws AuthException if user is not part of project or has insufficient permissions
     */
    public function requireProjectAccess(int $projectId, AccessRole $routeReqAccess): UserRole;

    /** Checks if user is already logged in when accessing login/signup pages
     * @throws AuthException if user is already logged in and tries to access login/signup pages
     */
    public function denyAuthenticatedOnAuthRoutes(string $routeName): void;
    //endregion
}