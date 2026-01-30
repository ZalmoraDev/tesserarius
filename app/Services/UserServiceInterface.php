<?php

namespace App\Services;

interface UserServiceInterface
{
    public function editAccount(string $newUsername, string $newEmail): void;

    public function deleteAccount(string $confirmName): void;
}