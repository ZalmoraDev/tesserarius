<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;
use PDO;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /** Retrieve a user by their id, returns UserIdentityDto or null if not found */
    public
    function findUserIdentityById(int $id): ?UserIdentityDto
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM users
                WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new UserIdentityDto(
            $row['id'],
            $row['username'],
            $row['email'],
        ) : null;
    }

    /** Retrieve a user by their email, returns UserIdentityDto or null if not found */
    public function findUserIdentityByEmail(string $email): ?UserIdentityDto
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM users
                WHERE email = :email'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new UserIdentityDto(
            $row['id'],
            $row['username'],
            $row['email']
        ) : null;
    }

    /** Retrieve a user by their username, returns UserIdentityDto or null if not found */
    public function findUserIdentityByUsername(string $username): ?UserIdentityDto
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM users
                WHERE username = :username'
        );

        $stmt->execute([
            'username' => $username
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new UserIdentityDto(
            $row['id'],
            $row['username'],
            $row['email']
        ) : null;
    }
}
