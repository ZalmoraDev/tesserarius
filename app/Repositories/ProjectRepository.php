<?php

namespace App\Repositories;

use App\Models\Project;
use PDO;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function getProjectByProjectId(int $projectId) : ?Project
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM projects WHERE id = :id");
            $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);
            $stmt->execute();

            if ($data = $stmt->fetch()) {
                return new Project(
                    $data["id"],
                    $data["invite_code"],
                    $data["name"],
                    $data["description"],
                    $data["created_at"]
                );
            }

            return null; // Return null if no project found

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return null; // Return null if an error occurs
        }
    }

    public function getProjectsByUserAndRole(int $userId, string $role): array
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT p.id, p.name, p.invite_code, p.description, p.created_at, u.username AS admin_username
                FROM projects p
                INNER JOIN project_members pm ON p.id = pm.project_id
                INNER JOIN project_members admin_pm ON admin_pm.project_id = p.id AND admin_pm.role = 'admin'
                INNER JOIN users u ON admin_pm.user_id = u.id
                WHERE pm.user_id = :userId AND pm.role = :role
            ");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            $projects = []; // Initialize projects array

            while ($data = $stmt->fetch()) {
                $project = new Project(
                    $data["id"],
                    $data["invite_code"],
                    $data["name"],
                    $data["description"],
                    $data["created_at"]
                );

                $project->setAdmin($data["admin_username"]); // Not part of constructor
                $projects[] = $project;
            }

            return $projects;

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return []; // Return empty array if an error occurs
        }
    }
}