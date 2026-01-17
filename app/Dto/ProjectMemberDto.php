<?php

namespace App\Dto;

use App\Models\Enums\UserRole;

/**
 * BASE: Project model
 *
 * JOIN: project_members 'role <- userRole' (DB only)
 *
 * JOIN: users 'name <- ownerName' (DB only)
 *
 * Used for listing projects in a dashboard view
 */
final readonly class ProjectMemberDto
{
    public function __construct(

        public int      $projectId,
        public int      $userId,
        public string   $userName, // JOINed from users table
        public string   $userEmail, // JOINed from users table
        public UserRole $userRole,
        public string   $joinedAt, // TODO: Change to DateTime
    )
    {
    }
}