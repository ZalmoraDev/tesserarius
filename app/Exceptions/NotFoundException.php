<?php

namespace App\Exceptions;

final class NotFoundException extends \RuntimeException
{
    public const string USER_NOT_FOUND = 'user_not_found';
    public const string PROJECT_NOT_FOUND = 'project_not_found';
    public const string TASK_NOT_FOUND = 'task_not_found';
}