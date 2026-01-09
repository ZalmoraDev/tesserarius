<?php

namespace App\Repositories;

use PDO;

class BaseRepository
{
    protected PDO $connection;

    function __construct()
    {
        try {
            $dsn = "{$_ENV['DB_TYPE']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
            $this->connection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            // TODO: Handle connection error appropriately
            echo "Connection failed: " . $e->getMessage();
        }
    }
}