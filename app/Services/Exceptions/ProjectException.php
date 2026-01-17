<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class ProjectException extends \RuntimeException
{
    // Duplicate projectname on bases of user's projects
    public const string FIELDS_REQUIRED = 'fields_required';

    public const string NAME_INVALID = 'name_invalid';
    public const string NAME_TAKEN = 'name_duplicate';

    public const string DESCRIPTION_INVALID = 'description_invalid';
    public const string REGISTRATION_FAILED = 'creation_failed';
}