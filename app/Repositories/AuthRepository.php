<?php

namespace App\Repositories;

use App\Models\User;
use PDO;

final class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    /** Create a new user in the database, after the service has validated the data */
    public function createUser(string $username, string $email, string $passwordHash): ?int
    {
        $stmt = $this->connection->prepare("
                INSERT INTO users (username, password_hash, email) 
                VALUES (:username, :passwordHash, :email)
            ");

        $stmt->execute([
            'username' => $username,
            'passwordHash' => $passwordHash,
            'email' => $email
        ]);

        // Only this PDO connection is referenced,
        // so there is no risk of fetching other user's data.
        // If lastInsertId fails, return null to indicate failure.
        $id = $this->connection->lastInsertId();
        if ($id === false) {
            return null;
            //throw new AuthException('Insert succeeded but no ID returned');
        }

        return (int) $id;
    }

    /** Retrieve a user by their id, returns User model or null if not found */
    public function getUserById(int $id): ?User
    {
        $stmt = $this->connection->prepare('
                SELECT * FROM users WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ternary to return User model or null
        return $data ? new User(
            $data['id'],
            $data['username'],
            $data['password_hash'],
            $data['email'],
            $data['created_at']
        ) : null;
    }


    /** Retrieve a user by their email, returns User model or null if not found */
    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->connection->prepare('
                SELECT * FROM users WHERE email = :email'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ternary to return User model or null
        return $data ? new User(
            $data['id'],
            $data['username'],
            $data['password_hash'],
            $data['email'],
            $data['created_at']
        ) : null;
    }

    /** Retrieve a user by their username, returns User model or null if not found */
    public function getUserByUsername(string $username): ?User
    {
        $stmt = $this->connection->prepare('
                SELECT * FROM users WHERE username = :username'
        );

        $stmt->execute([
            'username' => $username
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ternary to return User model or null
        return $data ? new User(
            $data['id'],
            $data['username'],
            $data['password_hash'],
            $data['email'],
            $data['created_at']
        ) : null;
    }

    // TODO: Validate this method, also no idea where this is used

    /** Retrieve the role of a user in a specific project, for router access control */
    public function getUserProjectRole(int $projectId, int $userId): ?string
    {
        $stmt = $this->connection->prepare('
                SELECT pm.role
                FROM project_members pm
                WHERE pm.user_id = :userId
                  AND pm.project_id = :projectId
            ');

        $stmt->execute([
            'userId' => $userId,
            'projectId' => $projectId
        ]);

        return $stmt->fetchColumn() ?: null;
    }
}
