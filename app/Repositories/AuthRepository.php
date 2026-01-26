<?php

namespace App\Repositories;

use App\Dto\UserAuthDto;
use App\Dto\UserIdentityDto;
use App\Models\Enums\UserRole;
use PDO;

final class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    /** Create a new user in the database, after the service has validated the data */
    public function createUser(string $username, string $email, string $passwordHash): ?UserIdentityDto
    {
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
    }

    /** Retrieve a user by their email, returns User model or null if not found */
    public function findAuthByEmail(string $email): ?UserAuthDto
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
        return $row ? new UserAuthDto(
            $row['id'],
            $row['password_hash']
        ) : null;
    }

    /** Retrieve the role of a user in a specific project, for router access control */
    public function findUserProjectRole(int $projectId, int $userId): ?UserRole
    {
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
    }
}
