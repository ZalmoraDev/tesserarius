<?php

namespace App\Views\Components;

use App\Core\Escaper;
use App\Dto\ProjectListItemDto;

class projectHomeTabComp
{
    public function printProjectsTabs(ProjectListItemDto $project): string
    {
        $id = Escaper::html($project->id) ?? "";
        $name = Escaper::html($project->name) ?? "";
        $description = Escaper::html($project->description) ?? "";
        $ownerName = Escaper::html($project->ownerName) ?? "";

        return "       
        <a class='tess-project-card cursor-pointer hover:brightness-50 flex flex-col justify-between min-h-32' href='" . "/project/view/{$id}'>
        <div>
            <span class='text-white block truncate'>$name</span>
            <span class='text-xs font-medium line-clamp-3'>$description</span>
        </div>
        <span class='text-xs font-medium'>Created by: $ownerName</span>
        </a>
    ";
    }

    public function printAddProjectTab(): string
    {
        return "       
    <a href='/project/create' class='tess-project-card flex flex-col items-center justify-center text-6xl font-bold cursor-pointer hover:brightness-50'>
        <span>+</span>
        <span class='text-lg font-medium'>New Project</span>
    </a>
    ";
    }
}