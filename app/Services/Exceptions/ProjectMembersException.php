<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class ProjectMembersException extends \RuntimeException
{
    public const string USER_COULD_NOT_BE_DELETED = 'User could not be deleted from project.';
    public const string INVITE_CODE_INVALID = 'The provided invite code is invalid.';
    public const string INVITE_CODE_EXPIRED = 'The provided invite code has expired.';
    // TODO: Fill out further
}