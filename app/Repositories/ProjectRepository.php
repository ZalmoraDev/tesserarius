<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Models\Enums\UserRole;
use App\Models\Project;
use PDO;

final class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    /** Used when accessing a project by its ID.
     *
     * Such as accessing it's URL /project/{projectId}
     */
    public function getProjectByProjectId(int $projectId): ?Project
    {
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
    }

    /** Retrieve projects for a user based on their roles.
     *
     * Used for populating the dashboard with projects the user is involved in.
     */
    public function getProjectListItemsByUserId(int $userId): array
    {
        $stmt = $this->connection->prepare("
                SELECT p.id, p.name, p.description, pm.role
                FROM projects p
                INNER JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = :userId
            ");
        $stmt->execute([
            'userId' => $userId
        ]);

        $projects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $project = new ProjectListItemDto(
                $row['id'],
                $row['name'],
                $row['description'],
                UserRole::from($row['role_enum']) // Convert string to UserRole enum
            );
            $projects[] = $project;
        }

        return $projects;
    }
}