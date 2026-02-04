<?php

namespace App\Models;

use DateTimeImmutable;
use JsonSerializable;

/** 1:1 mapping to 'projects' DB table */
final readonly class Project
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
}