<?php

namespace App\Models\Enums;

/** Used in router comparisons for access control
 * Validated based on route access requirements
 * against these role int values
 */
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