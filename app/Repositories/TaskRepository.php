<?php

namespace App\Repositories;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Exceptions\TaskRepositoryException;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use DateTimeImmutable;
use PDO;

final class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function getAllProjectTasks(int $projectId): array
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT task_id, project_id, title, description, status, priority, 
                       created_by, assignee_id, created_at, due_date
                FROM tasks
                WHERE project_id = :projectId
                ORDER BY created_at ASC
            ");
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            $stmt->execute();

            $tasks = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task(
                    (int)$data["task_id"],
                    (int)$data["project_id"],
                    $data["title"],
                    $data["description"],
                    TaskStatus::from($data["status"]),
                    TaskPriority::from($data["priority"]),
                    (int)$data["created_by"],
                    $data["assignee_id"] ? (int)$data["assignee_id"] : null,
                    new DateTimeImmutable($data["created_at"]),
                    $data["due_date"] ? new DateTimeImmutable($data["due_date"]) : null
                );
                $tasks[] = $task;
            }
            return $tasks;

        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function createTask(
        Task $task,
        int  $creatorId
    ): Task
    {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO tasks (project_id, title, description, status, priority, created_by, assignee_id, due_date, created_at)
                VALUES (:project_id, :title, :description, :status, :priority, :created_by, :assignee_id, :due_date, NOW())
                RETURNING task_id, created_at
            ");

            $projectId = $task->projectId;
            $title = $task->title;
            $description = $task->description;
            $statusValue = $task->status->value;
            $priorityValue = $task->priority->value;
            $assigneeId = $task->assigneeId;
            $dueDateStr = $task->dueDate?->format('Y-m-d H:i:s');

            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':status', $statusValue, PDO::PARAM_STR);
            $stmt->bindParam(':priority', $priorityValue, PDO::PARAM_STR);
            $stmt->bindParam(':created_by', $creatorId, PDO::PARAM_INT);
            $stmt->bindParam(':assignee_id', $assigneeId, PDO::PARAM_INT);
            $stmt->bindParam(':due_date', $dueDateStr, PDO::PARAM_STR);

            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) {
                throw new TaskRepositoryException("Failed to create task");
            }

            // Return new Task with input data + database-generated fields
            return new Task(
                id: (int)$data['task_id'],
                projectId: $task->projectId,
                title: $task->title,
                description: $task->description,
                status: $task->status,
                priority: $task->priority,
                creatorId: $creatorId,
                assigneeId: $task->assigneeId,
                creationDate: new DateTimeImmutable($data['created_at']),
                dueDate: $task->dueDate
            );
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new TaskRepositoryException("Failed to create task: " . $e->getMessage());
        }
    }

// Function to update a task's column in the database
    public function changeTaskStatus(int $taskId, string $newColumn): bool
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

    public function updateTask(Task $task): Task
    {
        try {
            $stmt = $this->connection->prepare("
                UPDATE tasks 
                SET title = :title, 
                    description = :description, 
                    status = :status, 
                    priority = :priority, 
                    assignee_id = :assignee_id, 
                    due_date = :due_date
                WHERE task_id = :task_id
            ");

            $taskId = $task->id;
            $title = $task->title;
            $description = $task->description;
            $statusValue = $task->status->value;
            $priorityValue = $task->priority->value;
            $assigneeId = $task->assigneeId;
            $dueDateStr = $task->dueDate?->format('Y-m-d H:i:s');

            $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':status', $statusValue, PDO::PARAM_STR);
            $stmt->bindParam(':priority', $priorityValue, PDO::PARAM_STR);
            $stmt->bindParam(':assignee_id', $assigneeId, PDO::PARAM_INT);
            $stmt->bindParam(':due_date', $dueDateStr, PDO::PARAM_STR);

            $success = $stmt->execute();

            if (!$success) {
                throw new TaskRepositoryException("Failed to update task");
            }

            // Return the same task object since all fields are already up-to-date
            return $task;
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new TaskRepositoryException("Failed to update task: " . $e->getMessage());
        }
    }

    public function deleteTask(int $taskId): bool
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM tasks WHERE task_id = :task_id");
            $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
}