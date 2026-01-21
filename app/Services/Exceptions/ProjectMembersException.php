<?php

namespace App\Services\Exceptions;

/** Exceptions for project members related errors. */
final class ProjectMembersException extends \RuntimeException
{
    public const string USER_COULD_NOT_BE_DELETED = 'Failed to remove user from project,<br>please try again later.';
    public const string INVITE_CODE_INVALID = 'The invite code is invalid.';
    public const string INVITE_CODE_EXPIRED = 'The invite code has expired.';
    public const string INVITE_REMOVAL_FAILED = 'Failed to remove invite code,<br>please try again later.';
}