<?php

namespace App\Controllers\Api;

use App\Services\Interfaces\TaskServiceInterface;

class TaskControllerApi
{
    private TaskServiceInterface $taskService;

    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    public function handleCreation(): void
    {

    }

    public function handleEdit(): void
    {
        // Get the task_id and new_column values from the POST data (API call)
        $taskId = $_POST['task_id'] ?? null;
        $newColumn = $_POST['new_column'] ?? null;

        if (!$taskId || !$newColumn) {
            // Return error if task_id or new_column is not provided
            echo json_encode(['status' => 'error', 'message' => 'Missing task ID or column']);
            return;
        }

        // Pass the string column name directly to the service
        $result = $this->taskService->editTask($taskId, $newColumn);

        // Return a success or error message based on the result
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Task moved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move task']);
        }
    }

    public function handleDeletion(): void
    {

    }
}