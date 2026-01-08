<?php

namespace App\Models\Enums;

/// Expresses minimum required access, not actual user role
enum AccessRole: int
{
    // Access levels
    case Anyone = 1;
    case Authenticated = 2;

    // Project roles, still matched for Authenticated users in router
    case Member = 3;
    case Admin = 4;
    case Owner = 5;
}