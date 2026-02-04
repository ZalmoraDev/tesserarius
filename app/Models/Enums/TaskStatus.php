<?php

namespace App\Models\Enums;

/** Task statuses within Project context */
enum TaskStatus: string
{
    case Backlog = 'Backlog'; // DEFAULT
    case ToDo = 'ToDo';
    case Doing = 'Doing';
    case Review = 'Review';
    case Done = 'Done';
}