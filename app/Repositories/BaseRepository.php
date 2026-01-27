<?php

namespace App\Repositories;

use PDO;

/** Base repository providing a PDO connection to be used by child repositories */
abstract class BaseRepository
{
    protected PDO $connection;

    function __construct()
    {
        $dsn = "{$_ENV['DB_TYPE']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
        $this->connection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}