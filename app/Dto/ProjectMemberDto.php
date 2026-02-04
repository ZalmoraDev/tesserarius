<?php

namespace App\Dto;

use App\Models\Enums\UserRole;
use DateTimeImmutable;

/**
 * BASE: Project model
 *
 * JOIN: project_members 'role <- userRole' (DB only)
 *
 * JOIN: users 'username, email <- userUsername, userEmail' (DB only)
 */
final readonly class ProjectMemberDto
{
    public function __construct(

        public int      $projectId,
        public int      $userId,
        public string   $username, // JOINed from users table
        public string   $userEmail, // JOINed from users table
        public UserRole $userRole, //  JOINed from project_members table
        public DateTimeImmutable $joinedAt
    )
    {
    }
}