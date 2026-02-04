<?php

namespace App\Services\Exceptions;

/** Exceptions for project members related errors. */
final class ProjectMembersException extends ServiceException
{
    public const string INVITE_CODE_INVALID = 'The invite code is invalid.';
    public const string INVITE_EXPIRATION_INVALID = 'The invite expiration date cannot be in the past.';
    public const string INVITE_COUNT_INVALID = 'The number of invite codes to generate is invalid. (choose 1-10)';
}

