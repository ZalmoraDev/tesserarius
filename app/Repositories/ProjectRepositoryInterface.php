<?php

namespace App\Repositories;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function findProjectByProjectId(int $projectId): ?Project; // TODO: Replace with DTO
    public function findProjectByName(string $name): ?Project; // TODO: Replace with DTO

    public function findProjectListItemsByUserId(int $userId): array;

    public function createProject(string $name, string $description): ?int;
}