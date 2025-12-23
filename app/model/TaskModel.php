<?php

namespace App\Model;

use DateTime;
use JsonSerializable;

class TaskModel implements JsonSerializable
{
    private int $id;
    private int $projectId;
    private string $title;
    private string $description;
    private string $columnName; // TODO: Change to Enum
    private string $createdAt; // TODO: Change to DateTime

    /**
     * @param int $id
     * @param int $projectId
     * @param string $title
     * @param string $description
     * @param string $columnName
     * @param string $createdAt
     */
    public function __construct(int $id, int $projectId, string $title, string $description, string $columnName, string $createdAt)
    {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->title = $title;
        $this->description = $description;
        $this->columnName = $columnName;
        $this->createdAt = $createdAt;
    }

    //-----------------------------------------------------
    // Standard getters and setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function setProjectId(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function setColumnName(string $columnName): void
    {
        $this->columnName = $columnName;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    //-----------------------------------------------------
    // Non-model getters and setters (retrieved through the repository join statements)



    //-----------------------------------------------------
    // JsonSerializable interface method


    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}