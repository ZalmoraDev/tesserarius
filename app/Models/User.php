<?php

namespace App\Models;

use DateTimeImmutable;

/** 1:1 mapping to 'users' DB table */
final readonly class User
{
    public function __construct(
        public int    $id,
        public string $username,
        public string $passwordHash,
        public string $email,
        public DateTimeImmutable $createdAt
    )
    {
    }
}