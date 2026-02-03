<?php

namespace App\Controllers\Api;

use App\Core\Csrf;
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
     * Performs common security checks for API endpoints:
     * - Sets JSON response header
     * - Verifies CSRF token
     * - Validates user authentication
     * - Optionally validates project access with required role
     *
     * @param int|null $projectId Optional project ID to validate access for
     * @param AccessRole $requiredRole Required access role for the project (default: Member)
     * @throws AuthException if CSRF verification, authentication, or project access fails
     */
    protected function authenticateRequest(?int $projectId, AccessRole $requiredRole): void
    {
        // Set JSON response header
        header('Content-Type: application/json');

        // Verify CSRF token
        Csrf::requireVerification($_POST['csrf'] ?? null);

        // Get current user ID from session
        $userId = $_SESSION['auth']['userId'] ?? null;
        if (!$userId) {
            $this->jsonError(401, 'Not authenticated');
            exit;
        }

        // If project ID is provided, validate project access
        if ($projectId !== null) {
            $this->authService->requireProjectAccess($projectId, $requiredRole);
        }
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
}
