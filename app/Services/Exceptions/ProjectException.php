<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class ProjectException extends \RuntimeException
{
    // NAME_INVALID is based on duplicate projectname of a user's projects
    public const string NAME_INVALID = 'name_invalid';
    public const string NAME_TAKEN = 'name_duplicate';

    public const string DESCRIPTION_INVALID = 'description_invalid';
    public const string REGISTRATION_FAILED = 'creation_failed';
    public const string EDIT_FAILED = 'edit_failed';
    public const string DELETION_FAILED = 'deletion_failed';
    public const string PROJECT_NOT_FOUND = 'project_not_found';
    public const string DELETION_NAME_MISMATCH = 'deletion_name_mismatch';
}