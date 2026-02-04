<?php

namespace App\Repositories;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\UserRole;
use App\Repositories\Exceptions\AuthRepoException;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use PDO;
use PDOException;

final class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function findAuthByEmail(string $email): ?UserAuthDto
    {
        try {
            $stmt = $this->connection->prepare('
                    SELECT *
                    FROM users
                    WHERE email = :email'
            );

            $stmt->execute([
                'email' => $email
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new UserAuthDto(
                $row['id'],
                $row['password_hash']
            ) : null;
        } catch (PDOException $e) {
            error_log("Database error in findAuthByEmail: " . $e->getMessage());
            throw new AuthRepoException(AuthRepoException::FAILED_TO_FIND_USER);
        }
    }

    public function findUserProjectRole(int $projectId, int $userId): ?UserRole
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT pm.role
            FROM project_members pm
            WHERE pm.user_id = :userId
              AND pm.project_id = :projectId
        ');

            $stmt->execute([
                'userId' => $userId,
                'projectId' => $projectId,
            ]);

            $role = $stmt->fetchColumn();

            return $role === false ? null : UserRole::from($role);
        } catch (PDOException $e) {
            error_log("Database error in findUserProjectRole: " . $e->getMessage());
            throw new AuthRepoException(AuthRepoException::FAILED_TO_FIND_ROLE);
        }
    }

    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto
    {
        try {
            $stmt = $this->connection->prepare('
                    INSERT INTO users (username, password_hash, email) 
                    VALUES (:username, :passwordHash, :email)'
            );

            $stmt->execute([
                'username' => $username,
                'passwordHash' => $passwordHash,
                'email' => $email
            ]);

            // Since this connection of the PDO is referenced,
            // there is no risk of fetching other user's data by race condition or similar
            $newId = $this->connection->lastInsertId();
            if ($newId === false)
                return null;

            return new UserIdentityDto($newId, $username, $email);
        } catch (PDOException $e) {
            error_log("Database error in createUser: " . $e->getMessage());
            throw new AuthRepoException(AuthRepoException::FAILED_TO_CREATE_USER);
        }
    }
}