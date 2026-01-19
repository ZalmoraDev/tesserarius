<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use PDO;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    /** Used when accessing a project by its ID.
     * Loads the full Project model to pass to the view, no DTO used as all data is needed */
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
            $row["id"],
            $row["owner_id"],
            $row["name"],
            $row["description"],
            $row["created_at"]
        ) : null;
    }

    /** Retrieves only the project name by its ID.
     * Used for project deletion name confirmation. */
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

    /** Checks if a project with the given name already exists.
     * Checked to enforce unique project names per user during creation. */
    public function projectExistsByName(string $name): bool
    {
        $stmt = $this->connection->prepare('
        SELECT EXISTS (
            SELECT 1
            FROM projects
            WHERE name = :name
        )'
        );

        $stmt->execute(['name' => $name]);

        return (bool)$stmt->fetchColumn();
    }

    /** Retrieve projects for a user based on their roles.
     *
     * Used for populating the homepage with projects the user is involved in.
     */
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

    /** Creates a new project for the currently logged-in user.
     * Returns the new project's ID for the controller to redirect to the new project page. */
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

    /** Edits an existing project's details.
     * @return bool: true if the project was updated, false if no changes were made.
     */
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

    /** Deletes a project by its ID.
     * @return bool: true if the project was deleted, false if no project was found with that ID.
     */
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
}