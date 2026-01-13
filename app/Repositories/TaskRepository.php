<?php

namespace App\Repositories;

use App\Models\Task;
use PDO;

final class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function getAllColumnTasks(int $projectId): array
    {
        // TODO: Replace with enum/TaskColumn.php
        $columnNames = ['backlog', 'to-do', 'doing', 'review', 'done'];
        $columns = []; // 1st dimension array to be filled with tasks arrays

        try {
            // For each column, fill an array with tasks of said column
            foreach ($columnNames as $columnName) {
                $stmt = $this->connection->prepare("
                SELECT *
                FROM tasks
                WHERE project_id = :projectId AND column_name = :columnName
            ");
                $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
                $stmt->bindParam(':columnName', $columnName);
                $stmt->execute();

                $columnTasks = [];

                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Create a task object for each task in current column
                    $task = new Task(
                        $data["id"],
                        $data["project_id"],
                        $data["title"],
                        $data["description"],
                        $data["column_name"],
                        $data["created_at"]
                    );
                    // Add the task object to the column tasks array
                    $columnTasks[] = $task;
                }
                // Add the 2D column array of tasks to the main 1D columns array
                $columns[] = $columnTasks;
            }
            return $columns; // return the 2D array of column tasks

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

// Function to update a task's column in the database
    public function moveTaskToColumn(int $taskId, string $newColumn): bool
    {
        try {
            // Directly use the string column names like 'backlog', 'todo', etc.
            $stmt = $this->connection->prepare("UPDATE tasks SET column_name = :newColumn WHERE id = :taskId");
            $stmt->bindParam(':newColumn', $newColumn);
            $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
}