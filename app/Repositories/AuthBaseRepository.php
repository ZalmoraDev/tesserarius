<?php

namespace App\Repositories;

use App\Models\Enums\UserRole;
use App\Models\User;
use PDO;

final class AuthBaseRepository extends BaseRepository
{
    public function createUser($username, $passwordHash, $email = null): bool
    {
        try {
            // If no email provided, generate a default one
            if ($email === null) {
                $email = $username . '@temp.com';
            }

            $stmt = $this->connection->prepare("
                INSERT INTO users (username, password_hash, email) 
                VALUES (:username, :passwordHash, :email)
            ");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':passwordHash', $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByUsername(string $username): ?User
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM users WHERE username = :userName");
            $stmt->bindParam(':userName', $username, PDO::PARAM_STR);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new User(
                    $data["id"],
                    $data["username"],
                    $data["password_hash"], // This is the password hash from the DB
                    $data["created_at"]
                );
            }
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }

        return null; // No user found
    }

    /// Retrieve the role of a user in a specific project, for router access control
    public function getUserProjectRole(int $projectId, int $userId): ?string
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT pm.role
                FROM project_members pm
                WHERE pm.user_id = :userId
                  AND pm.project_id = :projectId
            ");

            $stmt->execute([
                ':userId' => $userId,
                ':projectId' => $projectId,
            ]);

            $role = $stmt->fetchColumn();
            return $role ?: null;

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
}
