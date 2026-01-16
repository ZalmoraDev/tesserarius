<?php

namespace App\Services;

use App\Models\Project;

interface ProjectServiceInterface
{
    public function getHomeProjects(int $userId): array;
    public function createProject(string $name, string $description): ?int;

    public function getProjectByProjectId(int $projectId): ?Project; // TODO: Change to DTO

}