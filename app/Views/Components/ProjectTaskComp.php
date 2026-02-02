<?php

namespace App\Views\Components;

use App\Core\Escaper;
use App\Models\Enums\TaskStatus;
use App\Models\ProjectTask;

/** Project task component for rendering task cards and related UI elements */
final class ProjectTaskComp
{
    /** Render all task columns */
    public static function renderColumns(array $tasksByStatus): void
    {
        foreach (TaskStatus::cases() as $status) {
            $tasks = $tasksByStatus[$status->value] ?? [];
            self::renderColumn($status, $tasks);
        }
    }

    /** Render a task column with header, tasks, and add button */
    private static function renderColumn(TaskStatus $status, array $tasks): void
    {
        ?>
        <div class="tess-base-container-sm rounded-xl flex flex-col w-72 min-w-60 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-white"><?= Escaper::html($status->value) ?></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn-<?= Escaper::html($status->value) ?>" class="flex flex-col gap-2">
                <?php
                foreach ($tasks as $task) {
                    self::renderTask($task);
                }
                ?>
            </div>
            <div>
                <?php self::renderAddTask(); ?>
            </div>
        </div>
        <?php
    }

    /** Render a project task card */
    private static function renderTask(ProjectTask $task): void
    {
        $title = Escaper::html($task->title ?? "");
        $description = Escaper::html($task->description ?? "");
        $taskId = Escaper::html($task->id ?? "");
        ?>
        <div class='tess-project-card w-full flex flex-col justify-between h-44' data-task-id='<?= $taskId ?>'>
            <div>
                <span class='text-white block truncate'><?= $title ?></span>
                <p class='text-xs font-medium line-clamp-5 break-words hyphens-auto'>
                    <?= $description ?>
                </p>
            </div>
            <div>
                <div class='w-full flex justify-between items-center'>
                    <?php foreach (TaskStatus::cases() as $status): ?>
                        <button class='move-btn flex-1 border bg-neutral-600 border-neutral-700 text-white hover:brightness-50 cursor-pointer py-1 rounded'
                                data-move-to='<?= Escaper::html($status->value) ?>'
                                title='Move to <?= Escaper::html($status->value) ?>'>
                            <?= substr($status->value, 0, 1) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /** Render add task card */
    private static function renderAddTask(): void
    {
        ?>
        <div class='tess-project-card flex flex-col items-center justify-center h-auto cursor-pointer hover:brightness-50'>
            <span class='text-lg font-medium'>+ Add Task</span>
        </div>
        <?php
    }

    /** Render join project card */
    public static function renderJoinProject(): void
    {
        ?>
        <div class='tess-project-card flex flex-col items-center justify-center space-y-2'>
            <input type='text' placeholder='Enter Project Code' class='tess-input-sm'>
            <button class='tess-btn-pri'>
                Join
            </button>
        </div>
        <?php
    }
}
