<?php

namespace App\Repository;

use App\Model\UserModel;
use PDO;

class AuthRepository extends Repository
{
    public function getUserByUsername($username): ?UserModel
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM users WHERE BINARY username = :userName");
            $stmt->bindParam(':userName', $username, PDO::PARAM_STR);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new UserModel(
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

    public function shouldProjectBeAccessible($userAccessing, $projectIdAccessed): bool
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT pm.role
                FROM project_members pm
                WHERE pm.user_id = :userId AND pm.project_id = :projectId
            ");
            $stmt->bindParam(':userId', $userAccessing, PDO::PARAM_STR);
            $stmt->bindParam(':projectId', $projectIdAccessed, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0; // Return true if the user has access to the project
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }

        return false; // No access found
    }
}
