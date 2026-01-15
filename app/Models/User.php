<?php

namespace App\Models;

use JsonSerializable;

/** 1:1 correlation to 'users' table in the database */
final readonly class User implements JsonSerializable
{
    public function __construct(
        public int    $id,
        public string $username,
        public string $passwordHash,
        public string $email,
        public string $createdAt //TODO: Change to DateTime
    )
    {
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}