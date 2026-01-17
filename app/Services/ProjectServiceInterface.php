<?php

namespace App\Services;

use App\Models\Project;

interface ProjectServiceInterface
{
    public function getHomeProjects(int $userId): array;
    public function createProject(string $name, string $description): ?int;

    public function getProjectByProjectId(int $projectId): ?Project; // Needs all data, so use model and not DTO

}