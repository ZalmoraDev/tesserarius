<?php
namespace App\Models\Enums;

/** User roles within Project and Database context */
enum UserRole: string {
    case Member = 'Member';
    case Admin = 'Admin';
    case Owner = 'Owner';

    /** Converts UserRole's retrieved from DB (1-3) to AccessRole used by router for access authorization (1-5)
     *
     * For example:
     * Blocks non-members from accessing project routes.
     * Blocks members from accessing admin or owner routes, etc. */
    public function toAccessRole(): AccessRole
    {
        return match ($this) {
            self::Member => AccessRole::Member,
            self::Admin  => AccessRole::Admin,
            self::Owner  => AccessRole::Owner,
        };
    }
}