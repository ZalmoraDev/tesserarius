<?php

namespace App\Models;

use DateTimeImmutable;
use JsonSerializable;

final readonly class Project implements JsonSerializable
{
    public function __construct(

        public int               $id,
        public int               $ownerId,
        public string            $name,
        public ?string           $description,
        public DateTimeImmutable $createdAt
    )
    {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}