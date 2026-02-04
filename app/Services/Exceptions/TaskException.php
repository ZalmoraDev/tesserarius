<?php

namespace App\Services\Exceptions;

use RuntimeException;

/** Exceptions for task-related errors. */
final class TaskException extends ServiceException
{
    // Validation errors
    public const string INVALID_PROJECT_ID = 'Invalid project ID';
    public const string INVALID_TASK_ID = 'Invalid task ID';
    public const string INVALID_ASSIGNEE_ID = 'Invalid assignee ID';

    public const string TITLE_REQUIRED = 'Title is required';
    public const string TITLE_LENGTH_INVALID = 'Task title must be between 3 and 256 characters';
    public const string DESCRIPTION_TOO_LONG = 'Task description must not exceed 1000 characters';

    // Repository operation errors
    public const string CREATION_FAILED = 'Failed to create task: ';
    public const string UPDATE_FAILED = 'Failed to update task: ';
    public const string DELETION_FAILED = 'Failed to delete task';
    public const string DELETION_FAILED_WITH_REASON = 'Failed to delete task: ';
}
