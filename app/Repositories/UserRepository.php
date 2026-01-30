<?php

namespace App\Repositories;

use App\Dto\UserIdentityDto;
use PDO;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /** Retrieve a user by their id, returns UserIdentityDto or null if not found */
    public
    function findUserIdentityById(int $id): ?UserIdentityDto
    {
        $stmt = $this->connection->prepare('
                SELECT *
                FROM users
                WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new UserIdentityDto(
            $row['id'],
            $row['username'],
            $row['email'],
        ) : null;
    }

    public function existsByUsername(string $username): bool
    {
        // TODO: Implement existsByUsername() method.
        return false;
    }

    public function existsByEmail(string $email): bool
    {
        // TODO: Implement existsByEmail() method.
        return false;
    }

    public function updateUser(int $id, string $newUsername, string $newEmail): void
    {
        // TODO: Implement updateUser() method.
    }
}
