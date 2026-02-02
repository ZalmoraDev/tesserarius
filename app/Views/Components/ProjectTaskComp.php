<?php

namespace App\Views\Components;

use App\Core\Escaper;
use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;

/** Project task component for rendering task cards and related UI elements */
final class ProjectTaskComp
{
    //region Public Methods

    /** Render all task columns */
    public static function renderColumns(array $tasksByStatus): void
    {
        foreach (TaskStatus::cases() as $status) {
            $tasks = $tasksByStatus[$status->value] ?? [];
            self::renderColumn($status, $tasks);
        }
    }

    /** Render add task modal */
    public static function renderAddTaskModal(): void
    {
        ?>
        <!-- Add Task Modal -->
        <div id="addTaskModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-9999">
            <div class="tess-base-container-md rounded-xl p-6 w-full max-w-md relative">
                <!-- Header & Close Button -->
                <button id="closeModalBtn"
                        class="absolute top-6 right-6 hover:brightness-75 cursor-pointer text-2xl leading-none">
                    <img src="/assets/icons/close-FFF.svg" alt="Close" class="w-8 h-8">
                </button>
                <div class="w-full">
                    <h2 class="text-2xl font-bold mb-4 text-white text-center">Add New Task</h2>
                    <hr class='w-full px-4 border-neutral-600'>
                </div>
                <form id="addTaskForm" class="flex flex-col gap-4">
                    <!-- Title & Description -->
                    <div>
                        <label for="title" class="text-lg font-bold">Title:</label>
                        <input type="text" id="title" class="tess-input-md w-full" placeholder="Title [3-128]"
                               name="name"
                               required>
                    </div>
                    <div>
                        <label for="description" class="text-lg font-bold">Description:</label>
                        <textarea id="description" class="tess-input-md min-h-32 w-full"
                                  placeholder="Description [0-128]"
                                  name="description"></textarea>
                    </div>
                    <hr class='w-full px-4 border-neutral-600'>
                    <!-- Status & Priority -->
                    <div class="flex gap-2 items-center">
                        <label for="taskStatus" class="text-lg font-bold">Status:</label>
                        <select id="taskStatus" name="status"
                                class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                            <?php foreach (TaskStatus::cases() as $status): ?>
                                <option value="<?= Escaper::html($status->value) ?>">
                                    <?= Escaper::html($status->value) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex gap-2 items-center">
                        <label for="taskPriority" class="text-lg font-bold">Priority:</label>
                        <select id="taskPriority" name="priority"
                                class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                            <?php foreach (TaskPriority::cases() as $priority): ?>
                                <option value="<?= Escaper::html($priority->value) ?>">
                                    <?= Escaper::html($priority->value) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dates -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="expires_at" class="text-lg font-bold">Created:</label>
                            <p>-</p>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label for="expires_at" class="text-lg font-bold">Due:</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" required
                                   class="tess-input-md w-full">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="tess-btn-pri px-6 py-2 rounded-xl hover:brightness-75">
                        Add Task
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
    //endregion


    //region Private Methods
    /** Render a task column with header, tasks, and add button */
    private static function renderColumn(TaskStatus $status, array $tasks): void
    {
        ?>
        <div class="tess-base-container-sm rounded-xl flex flex-col w-72 min-w-60 gap-2">
            <div class="flex flex-col gap-2 w-full items-center">
                <h1 class="text-lg font-bold text-white"><?= Escaper::html($status->value) ?></h1>
                <hr class='w-full px-4 border-neutral-600'>
            </div>
            <div id="taskColumn-<?= Escaper::html($status->value) ?>" class="flex flex-col w-full gap-2">
                <?php
                foreach ($tasks as $task)
                    self::renderTask($task);
                self::renderAddTask($status); ?>
            </div>
        </div>
        <?php
    }

    /** Render a project task card */
    private static function renderTask(Task $task): void
    {
        $title = Escaper::html($task->title ?? "");
        $description = Escaper::html($task->description ?? "");
        $taskId = Escaper::html($task->id ?? "");
        ?>
        <div class='tess-project-card w-full flex flex-col justify-between h-44' data-task-id='<?= $taskId ?>'>
            <div>
                <span class='text-white block truncate'><?= $title ?></span>
                <p class='text-xs font-medium line-clamp-5 wrap-break-word hyphens-auto'>
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
    private static function renderAddTask(TaskStatus $status): void
    {
        ?>
        <button class='tess-project-card text-lg font-medium w-full flex flex-col items-center justify-center h-auto cursor-pointer hover:brightness-50 add-task-btn'
                data-status='<?= Escaper::html($status->value) ?>'>
            + Add Task
        </button>
        <?php
    }
    //endregion
}