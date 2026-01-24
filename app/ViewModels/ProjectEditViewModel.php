<?php

namespace App\ViewModels;

use App\Models\Project;

/** ViewModel for editing a project */
final readonly class ProjectEditViewModel {
    public function __construct(
        public Project $project,
        public array $members, // could be DTOs or ViewModels
        public array $invites  // could be DTOs or ViewModels
    ) {}
}
