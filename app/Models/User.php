<?php

namespace App\Models;

/** 1:1 correlation to 'users' table in the database, */
final readonly class User
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
}