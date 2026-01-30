<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;
use App\Repositories\Interfaces\UserRepositoryInterface;
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

    /** Checks if a user with the given username already exists.
     * @return bool true if exists, false otherwise */
    public function existsByUsername(string $username): bool
    {
        $stmt = $this->connection->prepare('
        SELECT EXISTS (
            SELECT 1
            FROM users
            WHERE username = :name
        )');

        $stmt->execute(['name' => $username]);

        return (bool)$stmt->fetchColumn();
    }

    /** Check if a user with the given email already exists.
     * @return bool true if exists, false otherwise */
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->connection->prepare('
        SELECT EXISTS (
            SELECT 1
            FROM users
            WHERE email = :email
        )');

        $stmt->execute(['email' => $email]);

        return (bool)$stmt->fetchColumn();
    }

    /** Update a user's username and email by their id */
    public function updateUser(int $id, string $newUsername, string $newEmail): void
    {
        $stmt = $this->connection->prepare('
            UPDATE users
            SET username = :username, email = :email
            WHERE id = :id
        ');

        $stmt->execute([
            'username' => $newUsername,
            'email' => $newEmail,
            'id' => $id
        ]);
    }

    /** Delete a user by their id
     * @return bool true if a row was deleted, false otherwise */
    public function deleteUser(int $id): bool
    {
        $stmt = $this->connection->prepare('
            DELETE FROM users
            WHERE id = :id
        ');

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}
