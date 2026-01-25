<?php
namespace App\Repositories\Exceptions;

use RuntimeException;

/** Abstract base exception class for repository-related errors. */
abstract class RepositoryException extends RuntimeException {}