<?php

namespace App\Repositories\Interfaces;

use App\Dto\ProjectListItemDto;
use App\Models\Project;
use App\Repositories\Exceptions\ProjectRepoException;
use DateMalformedStringException;

interface ProjectRepositoryInterface
{
    //region Retrieval
    /** Checks if a project with the given name already exists.
     * @return bool True if exists, false otherwise.
     * @throws ProjectRepoException if database query fails
     */
    public function existsByName(string $name): bool;

    /** Used when accessing a project by its ID.
     * @return Project|null The project if found, null otherwise.
     * @throws ProjectRepoException if database query fails
     * @throws DateMalformedStringException if date parsing fails
     */
    public function findProjectByProjectId(int $projectId): ?Project;

    /** Retrieves only the project name by ID.
     * @return string|null Name is found, null otherwise.
     * @throws ProjectRepoException if database query fails
     */
    public function findProjectNameByProjectId(int $projectId): ?string;

    /** Retrieve projects for a user based on their roles.
     * @return ProjectListItemDto[], can be empty.
     * @throws ProjectRepoException if database query fails
     */
    public function findProjectListItemsByUserId(int $userId): array;
    //endregion


    //region Modification
    /** Creates a new project for the currently logged-in user.
     * @return int New project's ID
     * @throws ProjectRepoException if database operation fails
     */
    public function createProject(int $ownerId, string $name, string $description): int;

    /** Edits an existing project's details.
     * @throws ProjectRepoException if project not found or database operation fails
     */
    public function editProject(int $projectId, string $name, string $description): void;

    /** Deletes a project by its ID.
     * @throws ProjectRepoException if project not found or database operation fails
     */
    public function deleteProject(int $projectId): void;
    //endregion
}