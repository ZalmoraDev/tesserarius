<?php
namespace App\Models\Enums;
enum TaskStatus: int {
    case Backlog = 0;
    case ToDo = 1;
    case Doing = 2;
    case Review = 3;
    case Done = 4;
}