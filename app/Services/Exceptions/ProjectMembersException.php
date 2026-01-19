<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class ProjectMembersException extends \RuntimeException
{
    public const string USER_COULD_NOT_BE_DELETED = 'user_could_not_be_deleted';
    public const string INVITE_CODE_INVALID = 'invite_code_invalid';
    public const string INVITE_CODE_EXPIRED = 'invite_code_expired';
    public const string INVITE_REMOVAL_FAILED = 'invite_removal_failed';
    // TODO: Fill out further
}