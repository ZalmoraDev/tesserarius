<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\Exceptions\ProjectMembers\InviteCodeExpiredOrUsedException;
use App\Repositories\Exceptions\ProjectMembers\InviteNotFoundException;
use App\Repositories\Exceptions\RepositoryException;
use App\Services\Exceptions\ProjectMembersException;
use DateTimeImmutable;
use PDO;

final class ProjectMembersRepository extends BaseRepository implements ProjectMembersRepositoryInterface
{
    /** Fetches all members of a project by project ID. */
    public function findProjectMembersByProjectId(int $projectId): array
    {
        $stmt = $this->connection->prepare('
        SELECT pm.project_id, pm.user_id, pm.role, pm.joined_at AS joined_at, u.username, u.email
        FROM project_members pm
        JOIN users u
            ON pm.user_id = u.id
        WHERE pm.project_id = :projectId
        ORDER BY pm.joined_at ASC'
        );

        $stmt->execute([
            'projectId' => $projectId
        ]);

        $members = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = new ProjectMemberDto(
                (int)$row['project_id'],
                (int)$row['user_id'],
                $row['username'],
                $row['email'],
                UserRole::from($row['role']),
                new DateTimeImmutable($row['joined_at'])
            );
        }
        return $members;
    }

    /** Fetches all invite codes for a project by project ID. */
    public function findProjectInviteCodes(int $projectId): array
    {
        $stmt = $this->connection->prepare('
        SELECT pi.id, pi.project_id, pi.invite_code, pi.expires_at, pi.used_at, pi.created_at, u.username AS creator_name
        FROM project_invites pi
        JOIN users u
            ON pi.created_by = u.id
        WHERE pi.project_id = :projectId
        ORDER BY pi.created_at DESC'
        );

        $stmt->execute([
            'projectId' => $projectId
        ]);

        $invites = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $invites[] = new ProjectInvite(
                (int)$row['id'],
                (int)$row['project_id'],
                $row['invite_code'],
                new DateTimeImmutable($row['expires_at']),
                $row['used_at'] ? new DateTimeImmutable($row['used_at']) : null,
                $row['creator_name'],
                new DateTimeImmutable($row['created_at'])
            );
        }

        return $invites;
    }


    /** Join a user to a project by invite code.
     * Returns the joined project ID on success.
     * @throws RepositoryException if invite code is invalid or used/expired.
     */
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int
    {
        // Start transaction, which on rollback undoes all the following query operations
        $this->connection->beginTransaction();

        // 1.1) Fetch invite details (FOR UPDATE locks the row for this transaction, making its access atomic)
        $stmt = $this->connection->prepare('
        SELECT project_id, expires_at, used_at
        FROM project_invites
        WHERE invite_code = :inviteCode
        FOR UPDATE'
        );
        $stmt->execute([
            'inviteCode' => $inviteCode
        ]);
        $invite = $stmt->fetch(PDO::FETCH_ASSOC);

        // 1.2) Check if invite exists
        if (!$invite) {
            $this->connection->rollBack();
            throw new InviteNotFoundException();
        }

        // 1.3) Check if invite is used or expired, uses DateTimeImmutable for comparison within PHP objects instead of SQL
        if ($invite['used_at'] !== null || new DateTimeImmutable($invite['expires_at']) < new DateTimeImmutable()) {
            $this->connection->rollBack();
            throw new InviteCodeExpiredOrUsedException;
        }

        // 2.1) Add user to project members
        $projectId = (int)$invite['project_id'];
        $stmt = $this->connection->prepare('
        INSERT INTO project_members (project_id, user_id, role, joined_at)
        VALUES (:projectId, :userId, :role, NOW())'
        );
        $stmt->execute([
            'projectId' => $projectId,
            'userId' => $userId,
            'role' => UserRole::Member->value
        ]);

        // 3) Mark invite as used
        $stmt = $this->connection->prepare('
        UPDATE project_invites
        SET used_at = NOW()
        WHERE invite_code = :inviteCode'
        );

        $stmt->execute([
            'inviteCode' => $inviteCode
        ]);

        // Unlocks the row and finalizes the transaction, returns id to redirect user to joined project's view
        $this->connection->commit();
        return $projectId;
    }

    /** Uses Service made invite-code(s), which contains projectId, creator etc.
     * Used by repository to create entry/entries in DB */
    public function createProjectInviteCodes(array $invites): bool
    {
        $stmt = $this->connection->prepare('
        INSERT INTO project_invites (project_id, invite_code, expires_at, created_by, created_at)
        VALUES (:projectId, :inviteCode, :expiresAt, 
            (SELECT id FROM users WHERE username = :createdBy), :createdAt)'
        );

        foreach ($invites as $invite) {
            $stmt->execute([
                'projectId' => $invite->projectId,
                'inviteCode' => $invite->inviteCode,
                'expiresAt' => $invite->expiresAt->format('Y-m-d H:i:s'),
                'createdBy' => $invite->createdBy,
                'createdAt' => $invite->createdAt->format('Y-m-d H:i:s')
            ]);
        }

        return true;
    }

    public function addProjectMember(int $projectId, int $userId, UserRole $role): void
    {
        $stmt = $this->connection->prepare('
        INSERT INTO project_members (project_id, user_id, role, joined_at)
        VALUES (:projectId, :userId, :role, NOW())'
        );

        $stmt->execute([
            'projectId' => $projectId,
            'userId' => $userId,
            'role' => $role->value
        ]);
    }

    public function removeProjectMember(int $projectId, int $userId): bool
    {
        // TODO: Implement removeProjectMember() method.
        return false;
    }

    public function removeProjectInviteCode(int $inviteId): bool
    {
        $stmt = $this->connection->prepare('
        DELETE FROM project_invites
        WHERE id = :inviteId'
        );

        $stmt->execute([
            'inviteId' => $inviteId
        ]);

        return $stmt->rowCount() > 0;
    }
}