<?php

namespace App\Dto;

/** DTO containing  */
final readonly class UserAuthDto
{
    public function __construct(
        public int    $id,
        public string $passwordHash
    )
    {
    }
}