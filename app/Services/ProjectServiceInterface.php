<?php

namespace App\Services;

use App\Models\Project;

interface ProjectServiceInterface
{
    public function getHomeProjects(int $userId): array;
    public function getProjectByProjectId(int $projectId): Project;

    public function createProject(string $name, string $description): int;
    public function editProject(int $projectId, string $name, string $description): void;
    public function deleteProject(int $projectId, string $confirmName): void;
}