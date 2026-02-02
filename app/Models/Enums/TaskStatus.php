<?php
namespace App\Models\Enums;
enum TaskStatus: string {
    case Backlog = 'Backlog';
    case ToDo = 'ToDo';
    case Doing = 'Doing';
    case Review = 'Review';
    case Done = 'Done';
}