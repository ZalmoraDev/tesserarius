<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;
use App\Repositories\Interfaces\UserRepositoryInterface;
use PDO;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    //region Retrieval
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

    public function findUserIdentityById(int $id): ?UserIdentityDto
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
    //endregion


    //region Modification
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
    //endregion
}
