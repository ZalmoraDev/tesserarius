<?php

namespace App\Repositories\Exceptions;

/** Exception for user repository operations. */
final class UserRepoException extends RepositoryException
{
    // Query errors
    public const string FAILED_TO_CHECK_USERNAME = "Failed to check if username exists";
    public const string FAILED_TO_CHECK_EMAIL = "Failed to check if email exists";
    public const string FAILED_TO_FETCH_USER = "Failed to fetch user";

    // Modification errors
    public const string FAILED_TO_UPDATE_USER = "Failed to update user";
    public const string FAILED_TO_DELETE_USER = "Failed to delete user";
    public const string USER_NOT_FOUND = "User not found";
}
