<?php

namespace App\Models;

use JsonSerializable;

final readonly class Project implements JsonSerializable
{
    public function __construct(

        public int     $id,
        public string  $inviteCode,
        public string  $name,
        public ?string $description,
        public string  $createdAt, // TODO: Change to DateTime
        public ?string $admin = null // Retrieved through the repository join statements
    )
    {
    }
    
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}