<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BorrowStatus;
use App\Events\BorrowDueSoon;
use App\Models\Borrow;
use Illuminate\Console\Command;

class NotifyDueSoon extends Command
{
    protected $signature = 'borrows:notify-due-soon';
    protected $description = 'Fire BorrowDueSoon event for borrows due tomorrow';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $borrows = Borrow::with('user', 'book')
            ->where('status', BorrowStatus::Active)
            ->whereDate('due_date', $tomorrow)
            ->get();

        foreach ($borrows as $borrow) {
            BorrowDueSoon::dispatch($borrow);
        }

        $this->info("Dispatched due-soon notifications for {$borrows->count()} borrow(s).");

        return self::SUCCESS;
    }
}
