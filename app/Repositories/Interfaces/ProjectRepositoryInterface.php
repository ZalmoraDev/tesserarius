<?php

namespace App\Repositories\Interfaces;

use App\Dto\ProjectListItemDto;
use App\Models\Project;

interface ProjectRepositoryInterface
{
    //region Retrieval
    /** Checks if a project with the given name already exists.
     * @return bool True if exists, false otherwise.
     */
    public function existsByName(string $name): bool;

    /** Used when accessing a project by its ID.
     * @return Project|null The project if found, null otherwise.
     */
    public function findProjectByProjectId(int $projectId): ?Project;

    /** Retrieves only the project name by ID.
     * @return string|null Name is found, null otherwise.
     */
    public function findProjectNameByProjectId(int $projectId): ?string;

    /** Retrieve projects for a user based on their roles.
     * @return ProjectListItemDto[], can be empty.
     */
    public function findProjectListItemsByUserId(int $userId): array;
    //endregion


    //region Modification
    /** Creates a new project for the currently logged-in user.
     * @return int|null New project's ID, or null if creation failed.
     */
    public function createProject(int $ownerId, string $name, string $description): ?int;

    /** Edits an existing project's details.
     * @return bool true if updated, false if not.
     */
    public function editProject(int $projectId, string $name, string $description): bool;

    /** Deletes a project by its ID.
     * @return bool true if deleted, false if not.
     */
    public function deleteProject(int $projectId): bool;
    //endregion
}