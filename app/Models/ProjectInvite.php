<?php

namespace App\Models;

use DateTimeImmutable;
use JsonSerializable;

final readonly class ProjectInvite implements JsonSerializable
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

    // TODO: Remove depending on JS use
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'projectId' => $this->projectId,
            'inviteCode' => $this->inviteCode,
            'expiresAt' => $this->expiresAt->format(DATE_ATOM),
            'usedAt' => $this->activatedAt?->format(DATE_ATOM),
            'createdBy' => $this->createdBy,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}