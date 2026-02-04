<?php

namespace App\Repositories\Exceptions;

/** Exception for authentication repository operations. */
final class AuthRepoException extends RepositoryException
{
    public const string FAILED_TO_FIND_USER = "Failed to find user by email";
    public const string FAILED_TO_FIND_ROLE = "Failed to find user project role";
    public const string FAILED_TO_CREATE_USER = "Failed to create user";
}
