<?php

namespace App\Services\Exceptions;

/** Exceptions for project-related errors. */
final class ProjectException extends ServiceException
{
    public const string PROJECT_NOT_FOUND = 'The requested project was not found.';

    public const string NAME_INVALID = 'The project name is invalid,<br>please try again.';
    public const string NAME_TAKEN = 'A project with this name already exists,<br>please choose a different name.';

    public const string DESCRIPTION_INVALID = 'The project description is invalid,<br>please try again.';
    public const string REGISTRATION_FAILED = 'Failed to create the project,<br>please try again later.';

    public const string EDIT_FAILED = 'Failed to update the project,<br>please try again later.';

    public const string DELETION_NAME_MISMATCH = 'The provided project name does not match the project.';
    public const string DELETION_FAILED = 'Failed to delete the project,<br>please try again later.';
}