<?php

namespace App\Repositories;

use App\Models\Enums\TaskPriority;
use App\Models\Enums\TaskStatus;
use App\Models\Task;
use App\Repositories\Exceptions\TaskRepoException;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use DateTimeImmutable;
use PDO;
use PDOException;

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

            $stmt->execute([
                'projectId' => $projectId
            ]);

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
        } catch (PDOException $e) {
            error_log("Database error in getAllProjectTasks: " . $e->getMessage());
            throw new TaskRepoException(TaskRepoException::FAILED_TO_FETCH_TASKS);
        }
    }

    public function createTask(Task $task, int $creatorId): Task
    {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO tasks (project_id, title, description, status, priority, created_by, assignee_id, due_date, created_at)
                VALUES (:project_id, :title, :description, :status, :priority, :created_by, :assignee_id, :due_date, NOW())
                RETURNING task_id, created_at
            ");

            $stmt->execute([
                'project_id' => $task->projectId,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status->value,
                'priority' => $task->priority->value,
                'created_by' => $creatorId,
                'assignee_id' => $task->assigneeId,
                'due_date' => $task->dueDate?->format('Y-m-d H:i:s')
            ]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data)
                throw new TaskRepoException(TaskRepoException::FAILED_TO_CREATE_TASK);

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
        } catch (PDOException $e) {
            error_log("Database error in createTask: " . $e->getMessage());
            throw new TaskRepoException(TaskRepoException::FAILED_TO_CREATE_TASK);
        }
    }

    public function changeTaskStatus(int $taskId, string $newStatus): void
    {
        try {
            $stmt = $this->connection->prepare("UPDATE tasks SET status = :newStatus WHERE task_id = :taskId");

            $stmt->execute([
                'newStatus' => $newStatus,
                'taskId' => $taskId
            ]);

            if ($stmt->rowCount() === 0)
                throw new TaskRepoException(TaskRepoException::TASK_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in changeTaskStatus: " . $e->getMessage());
            throw new TaskRepoException(TaskRepoException::FAILED_TO_UPDATE_TASK);
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

            $stmt->execute([
                'task_id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status->value,
                'priority' => $task->priority->value,
                'assignee_id' => $task->assigneeId,
                'due_date' => $task->dueDate?->format('Y-m-d H:i:s')
            ]);

            if ($stmt->rowCount() === 0)
                throw new TaskRepoException(TaskRepoException::TASK_NOT_FOUND);

            // Return the same task object since all fields are already up-to-date
            return $task;
        } catch (PDOException $e) {
            error_log("Database error in updateTask: " . $e->getMessage());
            throw new TaskRepoException(TaskRepoException::FAILED_TO_UPDATE_TASK);
        }
    }

    public function deleteTask(int $taskId): void
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM tasks WHERE task_id = :task_id");

            $stmt->execute([
                'task_id' => $taskId
            ]);

            if ($stmt->rowCount() === 0)
                throw new TaskRepoException(TaskRepoException::TASK_NOT_FOUND);
        } catch (PDOException $e) {
            error_log("Database error in deleteTask: " . $e->getMessage());
            throw new TaskRepoException(TaskRepoException::FAILED_TO_DELETE_TASK);
        }
    }
}