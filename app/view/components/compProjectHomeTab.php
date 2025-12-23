<?php

namespace App\View\Components;

use App\Model\ProjectModel;

class compProjectHomeTab
{
    public function printProjectsTabs(ProjectModel $project): string
    {
        $id = htmlspecialchars($project->getId()) ?? "";
        $name = htmlspecialchars($project->getName()) ?? "";
        $description = htmlspecialchars($project->getDescription()) ?? "";
        $admin = htmlspecialchars($project->getAdmin()) ?? "";

        return "       
        <a class='tess-project-card cursor-pointer hover:brightness-50 flex flex-col justify-between min-h-32' href='" . SITE_URL . "/project/view/{$id}'>
        <div>
            <span class='text-amber-400 block truncate'>$name</span>
            <span class='text-xs font-medium line-clamp-3'>$description</span>
        </div>
        <span class='text-xs font-medium'>Created by: $admin</span>
        </a>
    ";
    }

    public function printAddProjectTab(): string
    {
        //TODO: Redo design and implement functionality
        return "       
    <div class='tess-project-card flex flex-col items-center justify-center text-6xl font-bold cursor-pointer hover:brightness-50'>
        <span>+</span>
        <span class='text-lg font-medium'>New Project</span>
    </div>
    ";
    }

    public function printJoinProjectTab(): string
    {
        //TODO: Redo design and implement functionality
        return "       
    <div class='tess-project-card flex flex-col items-center justify-center space-y-2'>
        <input type='text' placeholder='Enter Project Code' class='tess-input-sm'>
        <button class='tess-btn-pri'>
            Join
        </button>
    </div>
    ";
    }
}
