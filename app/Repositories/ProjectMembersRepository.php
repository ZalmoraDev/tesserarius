<?php

namespace App\Repositories;

use App\Dto\ProjectListItemDto;
use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
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