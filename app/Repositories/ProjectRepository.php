<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use App\Repositories\Exceptions\ProjectRepoException;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use DateTimeImmutable;
use PDO;
use PDOException;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    //region Retrieval
    public function existsByName(string $name): bool
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT EXISTS (
                SELECT 1
                FROM projects
                WHERE name = :name
            )');

            $stmt->execute(['name' => $name]);

            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in existsByName: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_CHECK_NAME);
        }
    }

    public function findProjectByProjectId(int $projectId): ?Project
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in findProjectByProjectId: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_FETCH_PROJECT);
        }
    }

    public function findProjectNameByProjectId(int $projectId): ?string
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in findProjectNameByProjectId: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_FETCH_PROJECT_NAME);
        }
    }

    public function findProjectListItemsByUserId(int $userId): array
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in findProjectListItemsByUserId: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_FETCH_PROJECTS);
        }
    }
    //endregion


    //region Modification
    public function createProject(int $ownerId, string $name, string $description): int
    {
        try {
            $stmt = $this->connection->prepare('
                    INSERT INTO projects (owner_id, name, description) 
                    VALUES (:owner_id, :name, :description)'
            );

            $stmt->execute([
                'owner_id' => $ownerId,
                'name' => $name,
                'description' => $description
            ]);

            $newProjectId = $this->connection->lastInsertId();
            if ($newProjectId === false)
                throw new ProjectRepoException(ProjectRepoException::FAILED_TO_CREATE_PROJECT);

            return (int)$newProjectId;
        } catch (PDOException $e) {
            error_log("Database error in createProject: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_CREATE_PROJECT);
        }
    }

    public function editProject(int $projectId, string $name, string $description): void
    {
        try {
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

            if ($stmt->rowCount() === 0)
                throw new ProjectRepoException(ProjectRepoException::PROJECT_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in editProject: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_UPDATE_PROJECT);
        }
    }

    public function deleteProject(int $projectId): void
    {
        try {
            $stmt = $this->connection->prepare('
                    DELETE FROM projects 
                    WHERE id = :project_id'
            );

            $stmt->execute([
                'project_id' => $projectId
            ]);

            if ($stmt->rowCount() === 0)
                throw new ProjectRepoException(ProjectRepoException::PROJECT_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in deleteProject: " . $e->getMessage());
            throw new ProjectRepoException(ProjectRepoException::FAILED_TO_DELETE_PROJECT);
        }
    }
    //endregion
}