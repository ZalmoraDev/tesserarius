<?php

namespace App\Models;

use App\Models\Enums\TaskStatus;
use DateTimeImmutable;
use JsonSerializable;

final readonly class ProjectTask implements JsonSerializable
{
    public function __construct(
        public int    $id,
        public int    $projectId,
        public string $title,
        public string $description,
        public TaskStatus $status,
        public DateTimeImmutable $createdAt
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'projectId' => $this->projectId,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}