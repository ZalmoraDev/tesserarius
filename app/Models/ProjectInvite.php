<?php

namespace App\Models;

use DateTimeImmutable;
use JsonSerializable;

/** 1:1 mapping to 'project_invites' DB table */
final readonly class ProjectInvite
{
    public function __construct(

        public int                $id,
        public int                $projectId,
        public string             $inviteCode,
        public DateTimeImmutable  $expiresAt,
        public ?DateTimeImmutable $activatedAt,
        public string             $createdBy,
        public DateTimeImmutable  $createdAt,
    )
    {
    }
}