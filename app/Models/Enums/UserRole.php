<?php
namespace App\Models\Enums;

/** User roles within a project context */
enum UserRole: string {
    case Member = 'Member';
    case Admin = 'Admin';
    case Owner = 'Owner';


    // TODO: Validate if correlation to accessRole ints are correct
    public function toAccessRole(): AccessRole
    {
        return match ($this) {
            self::Member => AccessRole::Member,
            self::Admin  => AccessRole::Admin,
            self::Owner  => AccessRole::Owner,
        };
    }
}