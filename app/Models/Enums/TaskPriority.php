<?php

namespace App\Models\Enums;
enum TaskPriority: string
{
    case None = 'None';
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
}