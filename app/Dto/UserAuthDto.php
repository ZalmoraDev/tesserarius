<?php

namespace App\Dto;

/**
 * BASE: User model
 *
 * Used for authentication against DB stored password hash
 */
final readonly class UserAuthDto
{
    public function __construct(
        public int    $id,
        public string $passwordHash
    )
    {
    }
}