<?php
namespace App\Models\Enums;
enum ProjectRole: int { // TODO: Combine with User model
    case member = 1;
    case admin = 2;
}
?>