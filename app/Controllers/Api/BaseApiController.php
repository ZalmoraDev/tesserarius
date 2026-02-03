<?php

namespace App\Controllers\Api;

use App\Models\Enums\AccessRole;
use App\Models\Enums\UserRole;
use App\Services\Exceptions\AuthException;
use App\Services\Interfaces\AuthServiceInterface;

/**
 * Base class for API controllers that provides authentication and authorization helpers.
 * Ensures that API endpoints validate user access in real-time, preventing deleted/removed users
 * from performing actions via AJAX without page refresh.
 */
abstract class BaseApiController
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Validates that the current user is authenticated
     *
     * @throws AuthException if user is not authenticated
     */
    protected function requireAuthentication(): void
    {
        $this->authService->requireAuthentication(AccessRole::Authenticated);
    }

    /**
     * Validates that the current user has access to the specified project with the required role.
     * This prevents removed users from performing actions via AJAX without page refresh.
     *
     * @param int $projectId The project ID to check access for
     * @param AccessRole $requiredRole Minimum required access role (defaults to Member)
     * @return UserRole The user's role in the project
     * @throws AuthException if user doesn't have access or insufficient permissions
     */
    protected function requireProjectAccess(int $projectId, AccessRole $requiredRole = AccessRole::Member): UserRole
    {
        return $this->authService->requireProjectAccess($projectId, $requiredRole);
    }

    /**
     * Returns a JSON error response with appropriate HTTP status code
     *
     * @param int $statusCode HTTP status code
     * @param string $errorMessage Error message to return
     */
    protected function jsonError(int $statusCode, string $errorMessage): void
    {
        http_response_code($statusCode);
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    }

    /**
     * Returns a JSON success response
     *
     * @param int $statusCode HTTP status code
     * @param array $data Data to return in the response
     */
    protected function jsonSuccess(int $statusCode, array $data): void
    {
        http_response_code($statusCode);
        echo json_encode(array_merge(['success' => true], $data));
    }

    /**
     * Gets the current authenticated user ID from session
     *
     * @return int|null User ID or null if not authenticated
     */
    protected function getCurrentUserId(): ?int
    {
        return $_SESSION['auth']['userId'] ?? null;
    }
}
