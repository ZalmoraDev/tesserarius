<?php

namespace App\Views\components;

use App\Models\Task;

// TODO: Distgusting code, needs to be redone
class projectTaskComp
{
    public function printProjectTask(Task $task): string
    {
        $title = htmlspecialchars($task->title ?? "");
        $description = htmlspecialchars($task->description ?? "");
        $taskId = htmlspecialchars($task->id ?? "");

        return "
    <div class='tess-project-card flex flex-col justify-between h-44' data-task-id='$taskId'>
        <div>
            <span class='text-white block truncate'>$title</span>
            <p class='text-xs font-medium line-clamp-5 break-words hyphens-auto'>
                $description
            </p>
        </div>
        <div>
            <div class='w-full flex justify-between items-center'>
                <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded' data-move-to='1'>1</button>
                <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded' data-move-to='2'>2</button>
                <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded' data-move-to='3'>3</button>
                <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded' data-move-to='4'>4</button>
                <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded' data-move-to='5'>5</button>
            </div>
        </div>
    </div>
    ";
    }


    public function printAddProjectTask(): string
    {
        //TODO: Redo design and implement functionality
        return "       
            <div class='tess-project-card flex flex-col items-center justify-center h-auto cursor-pointer hover:brightness-50'>
                <span class='text-lg font-medium'>+ Add Task</span>
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
