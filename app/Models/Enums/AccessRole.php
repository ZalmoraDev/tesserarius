<?php

namespace App\Models\Enums;

/** Used in Router comparisons for access control.
 * Uses UserRole and adds non-project access roles (Anyone, Authenticated) */
enum AccessRole: int
{
    // Public access
    case Anyone = 1;
    case Authenticated = 2;

    // Project-role access
    case Member = 3;
    case Admin = 4;
    case Owner = 5;
}