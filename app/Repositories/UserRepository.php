<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;
use App\Repositories\Exceptions\UserRepoException;
use App\Repositories\Interfaces\UserRepositoryInterface;
use PDO;
use PDOException;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    //region Retrieval
    public function existsByUsername(string $username): bool
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT EXISTS (
                SELECT 1
                FROM users
                WHERE username = :name
            )');

            $stmt->execute(['name' => $username]);

            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in existsByUsername: " . $e->getMessage());
            throw new UserRepoException(UserRepoException::FAILED_TO_CHECK_USERNAME);
        }
    }

    public function existsByEmail(string $email): bool
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT EXISTS (
                SELECT 1
                FROM users
                WHERE email = :email
            )');

            $stmt->execute(['email' => $email]);

            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in existsByEmail: " . $e->getMessage());
            throw new UserRepoException(UserRepoException::FAILED_TO_CHECK_EMAIL);
        }
    }

    public function findUserIdentityById(int $id): ?UserIdentityDto
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in findUserIdentityById: " . $e->getMessage());
            throw new UserRepoException(UserRepoException::FAILED_TO_FETCH_USER);
        }
    }
    //endregion


    //region Modification
    public function updateUser(int $id, string $newUsername, string $newEmail): void
    {
        try {
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

            if ($stmt->rowCount() === 0)
                throw new UserRepoException(UserRepoException::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in updateUser: " . $e->getMessage());
            throw new UserRepoException(UserRepoException::FAILED_TO_UPDATE_USER);
        }
    }

    public function deleteUser(int $id): void
    {
        try {
            $stmt = $this->connection->prepare('
                DELETE FROM users
                WHERE id = :id
            ');

            $stmt->execute([
                'id' => $id
            ]);

            if ($stmt->rowCount() === 0)
                throw new UserRepoException(UserRepoException::USER_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in deleteUser: " . $e->getMessage());
            throw new UserRepoException(UserRepoException::FAILED_TO_DELETE_USER);
        }
    }
    //endregion
}