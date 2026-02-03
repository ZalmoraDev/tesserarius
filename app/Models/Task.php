<?php

namespace App\Models;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use DateTimeImmutable;
use JsonSerializable;

final readonly class Task implements JsonSerializable
{
    public function __construct(
        public int               $id,
        public int               $projectId,
        public string            $title,
        public ?string           $description,
        public TaskStatus        $status,
        public TaskPriority      $priority,
        public int               $creatorId,
        public ?int              $assigneeId,
        public DateTimeImmutable $creationDate,
        public ?DateTimeImmutable $dueDate
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
            'priority' => $this->priority->value,
            'creatorId' => $this->creatorId,
            'assigneeId' => $this->assigneeId,
            'creationDate' => $this->creationDate->format('Y-m-d H:i:s'),
            'dueDate' => $this->dueDate?->format('Y-m-d H:i:s')
        ];
    }
}