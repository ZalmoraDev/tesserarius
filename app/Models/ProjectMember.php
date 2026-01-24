<?php

namespace App\Models;

use App\Models\Enums\UserRole;
use DateTimeImmutable;
use JsonSerializable;

/** 1:1 mapping to 'project_members' DB table */
final readonly class ProjectMember implements JsonSerializable
{
    public function __construct(

        public int               $projectId,
        public int               $userId,
        public UserRole          $role,
        public DateTimeImmutable $joinedAt
    )
    {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}