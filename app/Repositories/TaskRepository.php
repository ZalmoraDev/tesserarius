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
                ORDER BY created_at DESC
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
                    new DateTimeImmutable($data["due_date"])
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
        int $projectId,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        int $creatorId,
        ?int $assigneeId,
        DateTimeImmutable $dueDate
    ): Task
    {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO tasks (project_id, title, description, status, priority, created_by, assignee_id, due_date, created_at)
                VALUES (:project_id, :title, :description, :status, :priority, :created_by, :assignee_id, :due_date, NOW())
                RETURNING task_id, project_id, title, description, status, priority, created_by, assignee_id, created_at, due_date
            ");

            $statusValue = $status->value;
            $priorityValue = $priority->value;
            $dueDateStr = $dueDate->format('Y-m-d H:i:s');

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

            return new Task(
                (int)$data['task_id'],
                (int)$data['project_id'],
                $data['title'],
                $data['description'],
                TaskStatus::from($data['status']),
                TaskPriority::from($data['priority']),
                (int)$data['created_by'],
                $data['assignee_id'] ? (int)$data['assignee_id'] : null,
                new DateTimeImmutable($data['created_at']),
                new DateTimeImmutable($data['due_date'])
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
}