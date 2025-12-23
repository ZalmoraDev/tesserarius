<?php

namespace App\Model;

use JsonSerializable;

class UserModel implements JsonSerializable
{
    private int $id;
    private string $username;
    private string $passwordHash;
    private string $createdAt; //TODO: Change to DateTime

    /**
     * @param int $id
     * @param string $username
     * @param string $passwordHash
     * @param string $createdAt
     */
    public function __construct(int $id, string $username, string $passwordHash, string $createdAt)
    {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}

?>