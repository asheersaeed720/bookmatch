<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('recommendations:generate')->daily();
Schedule::command('borrows:check-overdue')->daily();
Schedule::command('borrows:notify-due-soon')->daily();
