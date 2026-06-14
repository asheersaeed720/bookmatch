<?php

declare(strict_types=1);

namespace App\Enums;

enum BorrowStatus: string
{
    case Active   = 'active';
    case Returned = 'returned';
    case Overdue  = 'overdue';
}
