<?php

namespace App\Services\Interfaces;

use App\Models\Project;
use App\Services\Exceptions\ProjectException;

interface ProjectServiceInterface
{
    //region Retrieve
    /** Returns a project by its ID.
     * @throws ProjectException if the project is not found.
     */
    public function getProjectByProjectId(int $projectId): Project;

    /** Returns an array of projects owned by or shared with the specified user.
     * This is then split into 'Your' and 'Member' projects for display on the home page.
     * @return array{owned: Project[], member: Project[]}
     */
    public function getHomeProjects(int $userId): array;
    //endregion


    //region Modify
    /** Creates a new project for the currently logged-in user.
     * And returns its ID to the ProjectController for redirecting to the new project's page.
     * @return int ID of the newly created project.
     * @throws ProjectException if the name/description do not meet format requirements.
     */
    public function createProject(string $name, string $description): int;

    /** Edits an existing project's name and description.
     * @throws ProjectException if validation fails or the edit operation fails.
     */
    public function editProject(int $projectId, string $name, string $description): void;

    /** Deletes a project after confirming the project name.
     * @Throws ProjectException if validation fails or the deletion operation fails.
     */
    public function deleteProject(int $projectId, string $confirmName): void;
    //endregion
}