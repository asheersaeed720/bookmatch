<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BorrowStatus;
use App\Models\Borrow;
use Illuminate\Console\Command;

class CheckOverdueBorrows extends Command
{
    protected $signature = 'borrows:check-overdue';
    protected $description = 'Mark active borrows past their due date as overdue';

    public function handle(): int
    {
        $count = Borrow::where('status', BorrowStatus::Active)
            ->whereDate('due_date', '<', today())
            ->update(['status' => BorrowStatus::Overdue]);

        $this->info("Marked {$count} borrow(s) as overdue.");

        return self::SUCCESS;
    }
}
