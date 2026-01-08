<?php
namespace App\Models\Enums;
enum UserRole: int {
    case Member = 1;
    case Admin = 2;
    case Owner = 3;
}