<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Backlog = 'backlog';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Concluded = 'concluded';
    case Cancelled = 'cancelled';
}
