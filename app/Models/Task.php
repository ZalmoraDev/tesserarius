<?php

namespace App\Models;

use DateTime;
use JsonSerializable;

final readonly class Task implements JsonSerializable
{
    public function __construct(
        public int    $id,
        public int    $projectId,
        public string $title,
        public string $description,
        public string $columnName, // TODO: Change to Enum
        public string $createdAt // TODO: Change to DateTime
    )
    {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}