<?php

namespace App\Services\Exceptions;

use App\Repositories\Exceptions\RepositoryException;
use DateMalformedStringException;
use Exception;
use RuntimeException;

/** Base exception class for all service-level exceptions with common error constants. */
abstract class ServiceException extends RuntimeException
{
    // Common database and system errors
    public const string DATABASE_ERROR = 'A database error occurred. Please try again later.';
    public const string INVALID_DATE_FORMAT = 'Invalid date format provided.';
    public const string UNEXPECTED_ERROR = 'An unexpected error occurred. Please try again later.';

    /**
     * Helper object for handling repository calls and catching its exceptions.
     * Controller catches ServiceException, which every concrete service exception extends,
     * so we rethrow as the appropriate subclass here.
     *
     * If the service throws its own exceptions, then those are still caught by the controller
     * as they also extend ServiceException.
     *
     * @param callable $function The repository function to call.
     * @param string $serviceExceptionSubClass The service exception class to throw on error (Which exception subclass of ServiceException).
     * @param string $serviceFunction Which service function is calling, and caused the error.
     * @return mixed The result of the repository function. (int, string, array, void etc.)
     * @throws ServiceException subclass on error (AuthException, ProjectException etc.).
     */
    public static function handleRepoCall(callable $function, string $serviceExceptionSubClass, string $serviceFunction): mixed
    {
        try {
            return $function();
        } catch (RepositoryException $e) {
            error_log("Repository error in $serviceFunction: " . $e->getMessage());
            throw new $serviceExceptionSubClass(self::DATABASE_ERROR);
        } catch (DateMalformedStringException $e) {
            error_log("Date error in $serviceFunction: " . $e->getMessage());
            throw new $serviceExceptionSubClass(self::INVALID_DATE_FORMAT);
        } catch (Exception $e) {
            error_log("Unexpected error in $serviceFunction: " . $e->getMessage());
            throw new $serviceExceptionSubClass(self::UNEXPECTED_ERROR);
        }
    }
}
