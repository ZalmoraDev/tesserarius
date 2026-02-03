<?php

namespace App\Views\Components;

use App\Core\Csrf;
use App\Core\Escaper;
use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;

/** Project task component for rendering task cards and related UI elements */
final class ProjectTaskComp
{
    //region Public Methods
    /** Render all task columns */
    public static function renderColumns(array $tasksByStatus, array $members = [], ?int $currentUserId = null): void
    {
        // Create member lookup map for quick access
        $memberLookup = [];
        foreach ($members as $member) {
            $memberLookup[$member->userId] = $member->username;
        }

        foreach (TaskStatus::cases() as $status) {
            $tasks = $tasksByStatus[$status->value] ?? [];
            self::renderColumn($status, $tasks, $memberLookup, $currentUserId);
        }
    }

    /** Render add task modal */
    public static function renderAddTaskModal($currentUser, array $members = [], $project = null): void
    {
        $projectId = $project->id ?? 0;
        ?>
        <!-- Add Task Modal -->
        <div id="addTaskModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-9999">
            <div class="tess-base-container-md rounded-xl p-6 w-full max-w-md relative">
                <!-- Header & Close Button -->
                <button id="closeModalBtn"
                        class="absolute top-6 left-6 hover:brightness-75 cursor-pointer text-2xl leading-none">
                    <img src="/assets/icons/close_FFF.svg" alt="Close" class="w-8 h-8">
                </button>
                <div class="w-full">
                    <h2 class="text-2xl font-bold mb-4 text-white text-center">Add New Task</h2>
                    <hr class='w-full px-4 border-neutral-600'>
                </div>

                <!-- Task Form -->
                <form id="addTaskForm" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <input type="hidden" name="project_id" value="<?= $projectId ?>">

                    <!-- Title & Description -->
                    <div class="flex flex-col gap-2">
                        <div>
                            <label for="title" class="text-lg font-bold">Title*:</label>
                            <input type="text" id="title" class="tess-input-md w-full" placeholder="Title [3-128]"
                                   name="title"
                                   required>
                        </div>
                        <div>
                            <label for="description" class="text-lg font-bold">Description:</label>
                            <textarea id="description" class="tess-input-md min-h-32 w-full"
                                      placeholder="Description [0-128]"
                                      name="description"></textarea>
                        </div>
                    </div>

                    <!-- Status & Priority -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
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
                    </div>

                    <!-- Creator & Assignee -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="creator" class="text-lg font-bold">Creator:</label>
                            <p><?= $currentUser ?></p>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label for="taskAssignee" class="text-lg font-bold">Assignee:</label>
                            <select id="taskAssignee" name="assignee"
                                    class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                                <option value="">Select assignee</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= Escaper::html($member->userId) ?>">
                                        <?= Escaper::html($member->username) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="expires_at" class="text-lg font-bold">Created:</label>
                            <p>-</p>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label for="due_date" class="text-lg font-bold">Due:</label>
                            <input type="datetime-local" id="due_date" name="due_date"
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

    /** Render edit task modal */
    public static function renderEditTaskModal(array $members = [], $project = null): void
    {
        $projectId = $project->id ?? 0;
        ?>
        <!-- Edit Task Modal -->
        <div id="editTaskModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-9999">
            <div class="tess-base-container-md rounded-xl p-6 w-full max-w-md relative">
                <!-- Header & Close Button -->
                <button id="closeEditModalBtn"
                        class="absolute top-6 left-6 hover:brightness-75 cursor-pointer text-2xl leading-none">
                    <img src="/assets/icons/close_FFF.svg" alt="Close" class="w-8 h-8">
                </button>
                <div class="w-full">
                    <h2 class="text-2xl font-bold mb-4 text-white text-center">Edit Task</h2>
                    <hr class='w-full px-4 border-neutral-600'>
                </div>

                <!-- Task Form -->
                <form id="editTaskForm" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
                    <input type="hidden" name="project_id" value="<?= $projectId ?>">
                    <input type="hidden" id="edit_task_id" name="task_id" value="">

                    <!-- Title & Description -->
                    <div class="flex flex-col gap-2">
                        <div>
                            <label for="edit_title" class="text-lg font-bold">Title*:</label>
                            <input type="text" id="edit_title" class="tess-input-md w-full" placeholder="Title [3-128]"
                                   name="title"
                                   required>
                        </div>
                        <div>
                            <label for="edit_description" class="text-lg font-bold">Description:</label>
                            <textarea id="edit_description" class="tess-input-md min-h-32 w-full"
                                      placeholder="Description [0-128]"
                                      name="description"></textarea>
                        </div>
                    </div>

                    <!-- Status & Priority -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="edit_taskStatus" class="text-lg font-bold">Status:</label>
                            <select id="edit_taskStatus" name="status"
                                    class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                                <?php foreach (TaskStatus::cases() as $status): ?>
                                    <option value="<?= Escaper::html($status->value) ?>">
                                        <?= Escaper::html($status->value) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label for="edit_taskPriority" class="text-lg font-bold">Priority:</label>
                            <select id="edit_taskPriority" name="priority"
                                    class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                                <?php foreach (TaskPriority::cases() as $priority): ?>
                                    <option value="<?= Escaper::html($priority->value) ?>">
                                        <?= Escaper::html($priority->value) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Assignee -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="edit_created_at" class="text-lg font-bold">Created:</label>
                            <p id="edit_created_at_display">-</p>
                        </div>
                        <div class="flex gap-2 items-center">
                            <label for="edit_taskAssignee" class="text-lg font-bold">Assignee:</label>
                            <select id="edit_taskAssignee" name="assignee"
                                    class="tess-input-sm bg-neutral-800 text-white border-neutral-700 border-2 rounded-xl p-2">
                                <option value="">Select assignee</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?= Escaper::html($member->userId) ?>">
                                        <?= Escaper::html($member->username) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <hr class='w-full px-4 border-neutral-600'>
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <label for="edit_due_date" class="text-lg font-bold">Due:</label>
                            <input type="datetime-local" id="edit_due_date" name="due_date"
                                   class="tess-input-md w-full">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit"
                                class="tess-btn-pri bg-blue-600 hover:bg-blue-700 text-white flex-1 px-6 py-2 rounded-xl">
                            Update Task
                        </button>
                        <button type="button" id="deleteTaskBtn"
                                class="bg-red-600 hover:bg-red-700 cursor-pointer text-white flex-1 px-6 py-2 rounded-xl">
                            Delete Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    //endregion


    //region Private Methods
    /** Render a task column with header, tasks, and add button */
    private static function renderColumn(TaskStatus $status, array $tasks, array $memberLookup, ?int $currentUserId): void
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
                    self::renderTask($task, $memberLookup, $currentUserId);
                self::renderAddTask($status); ?>
            </div>
        </div>
        <?php
    }

    /** Render a project task card */
    private static function renderTask(Task $task, array $memberLookup, ?int $currentUserId): void
    {
        $title = Escaper::html($task->title ?? "");
        $description = Escaper::html($task->description ?? "");
        $taskId = Escaper::html($task->id ?? "");
        $status = Escaper::html($task->status->value ?? "");
        $priority = Escaper::html($task->priority->value ?? "");
        $assigneeId = Escaper::html($task->assigneeId ?? "");
        $dueDate = $task->dueDate ? $task->dueDate->format('Y-m-d\TH:i') : "";
        $createdAt = $task->creationDate ? $task->creationDate->format('Y-m-d H:i') : "";

        // Get assignee name from lookup
        $assigneeName = "";
        if ($task->assigneeId && isset($memberLookup[$task->assigneeId])) {
            $assigneeName = Escaper::html($memberLookup[$task->assigneeId]);
        }

        // Check if current user is the assignee
        $isCurrentUserAssignee = $currentUserId && $task->assigneeId === $currentUserId;

        // Format due date for display
        $dueDateDisplay = "";
        if ($task->dueDate) {
            $dueDateDisplay = $task->dueDate->format('M j, H:i');
        }

        // Determine circle color based on priority
        $priorityColorClass = match ($task->priority) {
            TaskPriority::Low => 'bg-blue-500',
            TaskPriority::Medium => 'bg-yellow-500',
            TaskPriority::High => 'bg-red-500',
            TaskPriority::None => 'bg-white',
        };
        ?>
        <div class='tess-project-card w-full flex flex-col h-32 cursor-pointer hover:brightness-75 task-card'
             data-task-id='<?= $taskId ?>'
             data-task-title='<?= $title ?>'
             data-task-description='<?= $description ?>'
             data-task-status='<?= $status ?>'
             data-task-priority='<?= $priority ?>'
             data-task-assignee='<?= $assigneeId ?>'
             data-task-assignee-name='<?= $assigneeName ?>'
             data-task-due-date='<?= $dueDate ?>'
             data-task-created-at='<?= $createdAt ?>'>
            <div class='flex-1 min-h-0 flex flex-col'>
                <div class='flex items-center gap-2 flex-shrink-0'>
                    <div class='<?= $priorityColorClass ?> w-3 h-3 rounded-full flex-shrink-0'></div>
                    <span class='text-white block truncate'><?= $title ?></span>
                </div>
                <p class='text-xs font-medium line-clamp-3 wrap-break-word hyphens-auto overflow-hidden'>
                    <?= $description ?>
                </p>
            </div>
            <div class='flex justify-between items-center text-xs text-neutral-400 mt-1 flex-shrink-0'>
                <div class='flex items-center gap-1'>
                    <?php if ($assigneeName): ?>
                        <?php if ($isCurrentUserAssignee): ?>
                            <img src='/assets/icons/account_FFF.svg' alt='Assignee' class='w-4 h-4'>
                        <?php endif; ?>
                        <span class='truncate'><?= $assigneeName ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($dueDateDisplay): ?>
                        <span><?= Escaper::html($dueDateDisplay) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /** Render add task card */
    private static function renderAddTask(TaskStatus $status): void
    {
        ?>
        <button class='tess-project-card text-lg font-medium w-full flex flex-col items-center justify-center h-auto cursor-pointer hover:brightness-75 add-task-btn'
                data-status='<?= Escaper::html($status->value) ?>'>
            + Add Task
        </button>
        <?php
    }
    //endregion
}