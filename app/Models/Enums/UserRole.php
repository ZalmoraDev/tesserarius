<?php
namespace App\Models\Enums;

/** User roles within a project context */
enum UserRole: string {
    case Member = 'Member';
    case Admin = 'Admin';
    case Owner = 'Owner';
}