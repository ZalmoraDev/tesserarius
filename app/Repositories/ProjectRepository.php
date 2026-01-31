<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use DateTimeImmutable;
use PDO;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    //region Retrieval
    public function existsByName(string $name): bool
    {
        $stmt = $this->connection->prepare('
        SELECT EXISTS (
            SELECT 1
            FROM projects
            WHERE name = :name
        )');

        $stmt->execute(['name' => $name]);

        return (bool)$stmt->fetchColumn();
    }

    public function findProjectByProjectId(int $projectId): ?Project
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM projects
                WHERE id = :id'
        );

        $stmt->execute([
            'id' => $projectId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Project(
            (int)$row["id"],
            (int)$row["owner_id"],
            $row["name"],
            $row["description"],
            new DateTimeImmutable($row["created_at"])
        ) : null;
    }

    public function findProjectNameByProjectId(int $projectId): ?string
    {
        $stmt = $this->connection->prepare('
        SELECT name
        FROM projects
        WHERE id = :id'
        );

        $stmt->execute([
            'id' => $projectId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['name'] : null;
    }

    public function findProjectListItemsByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare('
        SELECT p.id, p.name, p.description, u.username AS owner_name, pm.role
        FROM projects p
            JOIN project_members pm
                ON p.id = pm.project_id
            JOIN users u
                ON p.owner_id = u.id
        WHERE pm.user_id = :userId'
        );

        $stmt->execute([
            'userId' => $userId
        ]);

        $projects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $project = new ProjectListItemDto(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['owner_name'],
                UserRole::from($row['role']) // Convert string to UserRole enum
            );
            $projects[] = $project;
        }
        return $projects;
    }
    //endregion


    //region Modification
    public function createProject(int $ownerId, string $name, string $description): ?int
    {
        // A user must be logged in at this point, retrieve their user ID to set the owner_id
        $stmt = $this->connection->prepare('
                INSERT INTO projects (owner_id, name, description) 
                VALUES (:owner_id, :name, :description)'
        );

        $stmt->execute([
            'owner_id' => $ownerId,
            'name' => $name,
            'description' => $description
        ]);

        // Since this connection of the PDO is referenced,
        // there is no risk of fetching other user's data by race condition or similar
        $newProjectId = $this->connection->lastInsertId();
        if ($newProjectId === false)
            return null;

        return $newProjectId;
    }

    public function editProject(int $projectId, string $name, string $description): bool
    {
        $stmt = $this->connection->prepare('
                UPDATE projects 
                SET name = :name, description = :description
                WHERE id = :project_id'
        );

        $stmt->execute([
            'project_id' => $projectId,
            'name' => $name,
            'description' => $description
        ]);

        // If nothing was updated, return false, caught as exception in service layer
        return $stmt->rowCount() > 0;
    }

    public function deleteProject(int $projectId): bool
    {
        $stmt = $this->connection->prepare('
                DELETE FROM projects 
                WHERE id = :project_id'
        );

        $stmt->execute([
            'project_id' => $projectId
        ]);

        return $stmt->rowCount() > 0;
    }
    //endregion
}