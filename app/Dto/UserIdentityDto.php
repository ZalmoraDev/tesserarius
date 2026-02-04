<?php

namespace App\Dto;

/**
 * BASE: User model
 *
 * Used for identifying user information without sensitive data
 */
final readonly class UserIdentityDto
{
    public function __construct(
        public int    $id,
        public string $username,
        public string $email
    )
    {
    }
}