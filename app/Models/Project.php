<?php

namespace App\Models;

use JsonSerializable;

class Project implements JsonSerializable
{
    private int $id; 
    private string $inviteCode;
    private string $name;
    private ?string $description;
    private string $createdAt; // TODO: Change to DateTime

    private ?string $admin = null; // Retrieved through the repository join statements

    /**
     * @param int $id
     * @param string $inviteCode
     * @param string $name
     * @param string|null $description
     * @param string $createdAt
     */
    public function __construct(int $id, string $inviteCode, string $name, ?string $description, string $createdAt)
    {
        $this->id = $id;
        $this->inviteCode = $inviteCode;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
    }

    //-----------------------------------------------------
    // Standard getters and setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getInviteCode(): string
    {
        return $this->inviteCode;
    }

    public function setInviteCode(string $inviteCode): void
    {
        $this->inviteCode = $inviteCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
    public function getAdmin(): ?string
    {
        return $this->admin;
    }

    public function setAdmin(?string $admin): void
    {
        $this->admin = $admin;
    }

    //-----------------------------------------------------
    // JsonSerializable interface method
    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}