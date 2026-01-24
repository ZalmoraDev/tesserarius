<?php

namespace App\Controllers\Api;

use App\Services\TaskService;

class TaskControllerApi
{

    // TODO: Completely rework
    // add dependency injection

    private TaskService $taskService;

    public function __construct()
    {
        $this->taskService = new TaskService();
    }

    public function moveTask()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the task_id and new_column values from the POST data (API call)
            $taskId = $_POST['task_id'] ?? null;
            $newColumn = $_POST['new_column'] ?? null;

            if (!$taskId || !$newColumn) {
                // Return error if task_id or new_column is not provided
                echo json_encode(['status' => 'error', 'message' => 'Missing task ID or column']);
                return;
            }

            // Pass the string column name directly to the service
            $result = $this->taskService->moveTaskToColumn($taskId, $newColumn);

            // Return a success or error message based on the result
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Task moved successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to move task']);
            }
        } else {
            // Handle invalid HTTP method
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        }
    }
}