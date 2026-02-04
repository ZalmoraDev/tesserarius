<?php

namespace App\Repositories\Exceptions;

/** Exception for task repository operations */
final class TaskRepoException extends RepositoryException
{
    // Query errors
    public const string FAILED_TO_FETCH_TASKS = "Failed to fetch tasks";

    // Modification errors
    public const string FAILED_TO_CREATE_TASK = "Failed to create task";
    public const string FAILED_TO_UPDATE_TASK = "Failed to update task";
    public const string FAILED_TO_DELETE_TASK = "Failed to delete task";
    public const string TASK_NOT_FOUND = "Task not found";
}
