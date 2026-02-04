<?php

namespace App\Repositories\Exceptions;

/** Exception for project repository operations. */
final class ProjectRepoException extends RepositoryException
{
    // Query errors
    public const string FAILED_TO_CHECK_NAME = "Failed to check if project name exists";
    public const string FAILED_TO_FETCH_PROJECT = "Failed to fetch project";
    public const string FAILED_TO_FETCH_PROJECT_NAME = "Failed to fetch project name";
    public const string FAILED_TO_FETCH_PROJECTS = "Failed to fetch projects";

    // Modification errors
    public const string FAILED_TO_CREATE_PROJECT = "Failed to create project";
    public const string FAILED_TO_UPDATE_PROJECT = "Failed to update project";
    public const string FAILED_TO_DELETE_PROJECT = "Failed to delete project";
    public const string PROJECT_NOT_FOUND = "Project not found";
}
