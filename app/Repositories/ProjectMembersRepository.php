<?php

namespace App\Repositories;

use App\Dto\ProjectMemberDto;
use App\Models\Enums\UserRole;
use App\Models\ProjectInvite;
use App\Repositories\Exceptions\ProjectMembersRepoException;
use App\Repositories\Interfaces\ProjectMembersRepositoryInterface;
use DateMalformedStringException;
use DateTimeImmutable;
use PDO;
use PDOException;

final class ProjectMembersRepository extends BaseRepository implements ProjectMembersRepositoryInterface
{
    //region Member Retrieval

    public function findProjectMembersByProjectId(int $projectId): array
    {
        try {
            $stmt = $this->connection->prepare('
            SELECT pm.project_id, pm.user_id, pm.role, pm.joined_at AS joined_at, u.username, u.email
            FROM project_members pm
            JOIN users u
                ON pm.user_id = u.id
            WHERE pm.project_id = :projectId
            ORDER BY pm.joined_at'
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
        } catch (PDOException $e) {
            error_log("Database error in findProjectMembersByProjectId: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_FETCH_MEMBERS);
        }
    }

    public function findProjectInvitesByProjectId(int $projectId): array
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in findProjectInvitesByProjectId: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_FETCH_INVITES);
        }
    }
    //endregion


    //region Member Management
    public function addProjectMember(int $projectId, int $userId, UserRole $role): void
    {
        try {
            $stmt = $this->connection->prepare('
            INSERT INTO project_members (project_id, user_id, role, joined_at)
            VALUES (:projectId, :userId, :role, NOW())'
            );

            $stmt->execute([
                'projectId' => $projectId,
                'userId' => $userId,
                'role' => $role->value
            ]);
        } catch (PDOException $e) {
            error_log("Database error in addProjectMember: " . $e->getMessage());
            // Check for duplicate key violation (user already a member)
            if (str_contains($e->getMessage(), 'duplicate key') || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new ProjectMembersRepoException(ProjectMembersRepoException::MEMBER_ALREADY_EXISTS);
            }
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_ADD_MEMBER);
        }
    }

    public function promoteProjectMember(int $projectId, int $userId): void
    {
        try {
            $stmt = $this->connection->prepare('
            UPDATE project_members
            SET role = :newRole
            WHERE project_id = :projectId AND user_id = :userId'
            );

            $stmt->execute([
                'newRole' => UserRole::Admin->value,
                'projectId' => $projectId,
                'userId' => $userId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new ProjectMembersRepoException(ProjectMembersRepoException::MEMBER_NOT_FOUND);
            }
        } catch (PDOException $e) {
            error_log("Database error in promoteProjectMember: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_UPDATE_ROLE);
        }
    }

    public function demoteProjectMember(int $projectId, int $userId): void
    {
        // ...existing code...
    }

    public function removeProjectMember(int $projectId, int $userId): void
    {
        try {
            $stmt = $this->connection->prepare('
            DELETE FROM project_members
            WHERE project_id = :projectId AND user_id = :userId'
            );

            $stmt->execute([
                'projectId' => $projectId,
                'userId' => $userId
            ]);

            if ($stmt->rowCount() === 0) {
                throw new ProjectMembersRepoException(ProjectMembersRepoException::MEMBER_NOT_FOUND);
            }
        } catch (PDOException $e) {
            error_log("Database error in removeProjectMember: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_REMOVE_MEMBER);
        }
    }
    //endregion


    //region Invite Codes
    public function joinProjectByInviteCode(string $inviteCode, int $userId): int
    {
        try {
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
                throw new ProjectMembersRepoException(ProjectMembersRepoException::INVITE_NOT_FOUND);
            }

            // 1.3) Check if invite is used
            if ($invite['used_at'] !== null) {
                $this->connection->rollBack();
                throw new ProjectMembersRepoException(ProjectMembersRepoException::INVITE_ALREADY_USED);
            }

            // 1.4) Check if invite is expired
            if (new DateTimeImmutable($invite['expires_at']) < new DateTimeImmutable()) {
                $this->connection->rollBack();
                throw new ProjectMembersRepoException(ProjectMembersRepoException::INVITE_EXPIRED);
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
        } catch (PDOException $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            error_log("Database error in joinProjectByInviteCode: " . $e->getMessage());
            // Check if user is already a member
            if (str_contains($e->getMessage(), 'duplicate key') || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new ProjectMembersRepoException(ProjectMembersRepoException::MEMBER_ALREADY_EXISTS);
            }
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_ADD_MEMBER);
        }
    }

    public function createProjectInviteCodes(array $invites): bool
    {
        try {
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
        } catch (PDOException $e) {
            error_log("Database error in createProjectInviteCodes: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_CREATE_INVITES);
        }
    }

    public function deleteProjectInviteCode(int $projectId, int $inviteId): bool
    {
        try {
            $stmt = $this->connection->prepare('
            DELETE FROM project_invites
            WHERE id = :inviteId AND project_id = :projectId'
            );

            $stmt->execute([
                'inviteId' => $inviteId,
                'projectId' => $projectId
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Database error in deleteProjectInviteCode: " . $e->getMessage());
            throw new ProjectMembersRepoException(ProjectMembersRepoException::FAILED_TO_DELETE_INVITE);
        }
    }
    //endregion
}