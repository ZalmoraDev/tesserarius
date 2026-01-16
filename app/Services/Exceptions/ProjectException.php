<?php

namespace App\Services\Exceptions;

/** Exceptions for authentication or authorization errors. */
final class ProjectException extends \RuntimeException
{
    // Duplicate projectname on bases of user's projects
    public const string FIELDS_REQUIRED = 'fields_required';
    public const string NAME_TAKEN = 'project_name_duplicate';
    public const string NAME_INVALID = 'project_name_invalid';
    public const string DESCRIPTION_INVALID = 'project_description_invalid';
    public const string REGISTRATION_FAILED = 'project_creation_failed';
}