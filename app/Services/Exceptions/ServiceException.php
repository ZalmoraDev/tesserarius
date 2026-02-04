<?php

namespace App\Services\Exceptions;

use RuntimeException;

/** Base exception class for all service-level exceptions with common error constants. */
abstract class ServiceException extends RuntimeException
{
    // Common database and system errors
    public const string DATABASE_ERROR = 'A database error occurred. Please try again later.';
    public const string INVALID_DATE_FORMAT = 'Invalid date format provided.';
    public const string UNEXPECTED_ERROR = 'An unexpected error occurred. Please try again later.';
}
