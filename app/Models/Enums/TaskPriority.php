<?php

namespace App\Models\Enums;

/** Task priority levels within Project management context */
enum TaskPriority: string
{
    case None = 'None'; // DEFAULT
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
}