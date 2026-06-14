<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Console\Command;

class GenerateRecommendations extends Command
{
    protected $signature = 'recommendations:generate';
    protected $description = 'Generate book recommendations for all users';

    public function handle(RecommendationService $service): int
    {
        $users = User::all();

        $this->info("Generating recommendations for {$users->count()} users...");
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $service->generateForUser($user);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
