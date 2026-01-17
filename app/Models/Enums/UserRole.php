<?php
namespace App\Models\Enums;

/** User roles within a project context */
enum UserRole: string {
    case Member = 'Member';
    case Admin = 'Admin';
    case Owner = 'Owner';

    /** Converts UserRole (retrieved from DB) to AccessRole (Used in router access authorization)
     *
     * For example:
     *
     * Blocks non-members from accessing project routes.
     * Blocks members from accessing admin or owner routes, etc.
     */
    public function toAccessRole(): AccessRole
    {
        return match ($this) {
            self::Member => AccessRole::Member,
            self::Admin  => AccessRole::Admin,
            self::Owner  => AccessRole::Owner,
        };
    }
}