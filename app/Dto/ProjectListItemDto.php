<?php

namespace App\Dto;

use App\Models\Enums\UserRole;

/**
 * BASE: Project model
 *
 * JOIN: project_members (DB only)
 *
 * Used for listing projects in a dashboard view
 */
final readonly class ProjectListItemDto
{
    public function __construct(

        public int      $id,
        public string   $name,
        public ?string  $description,
        public UserRole $userRole //  JOINed from project_members table
    )
    {
    }
}