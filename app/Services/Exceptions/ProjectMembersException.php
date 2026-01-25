<?php

namespace App\Services\Exceptions;

/** Exceptions for project members related errors. */
final class ProjectMembersException extends \RuntimeException
{
    public const string INVITE_CODE_INVALID = 'The invite code is invalid.';
    public const string INVITE_CODE_EXPIRED_OR_USED = 'The invite code has been expired or used.';
    public const string INVITE_REMOVAL_FAILED = 'Failed to remove invite code,<br>please try again later.';

    public const string USER_COULD_NOT_BE_REMOVED = 'Failed to remove user from project,<br>please try again later.';

    public const string ADMIN_CANNOT_REMOVE_OTHER_ADMIN = 'An admin user cannot remove another admin user from the project.';
    public const string OWNER_CANNOT_BE_REMOVED = 'The project owner cannot be removed from the project.';
}