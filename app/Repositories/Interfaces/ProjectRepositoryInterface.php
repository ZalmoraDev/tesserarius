<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function findProjectByProjectId(int $projectId): ?Project;
    public function findProjectNameByProjectId(int $projectId): ?string;

    public function existsByName(string $name): bool;

    public function findProjectListItemsByUserId(int $userId): array;

    public function createProject(int $ownerId, string $name, string $description): ?int;

    public function editProject(int $projectId, string $name, string $description): bool;
    public function deleteProject(int $projectId): bool;
}