<?php

namespace App\Dto;

/** DTO containing basic user identity information */
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