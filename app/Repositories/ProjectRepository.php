<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use PDO;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{

    // TODO: Return DTOs instead of Model
    /** Used when accessing a project by its ID.
     *
     * Such as accessing it's URL /project/{projectId}
     */
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

    // TODO: Return DTOs instead of Model
    public function findProjectByName(string $name): ?Project
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM projects
                WHERE name = :name'
        );

        $stmt->execute([
            'name' => $name
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

    /** Retrieve projects for a user based on their roles.
     *
     * Used for populating the homepage with projects the user is involved in.
     */
    public function findProjectListItemsByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare('
        SELECT p.id, p.name, p.description, u.username AS owner_name, pm.role
        FROM projects p
            INNER JOIN project_members pm
                ON p.id = pm.project_id
            INNER JOIN users u
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
    public function createProject(string $name, string $description): ?int
    {
        // A user must be logged in at this point, retrieve their user ID to set the owner_id
        $ownerId = (int)$_SESSION['auth']['userId'];
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

    /** Adds a member to a project with a specified role. */
    public function addProjectMember(int $projectId, int $userId, UserRole $role): void
    {
        $stmt = $this->connection->prepare('
                INSERT INTO project_members (project_id, user_id, role) 
                VALUES (:project_id, :user_id, :role)'
        );

        $stmt->execute([
            'project_id' => $projectId,
            'user_id' => $userId,
            'role' => $role->value
        ]);
    }
}