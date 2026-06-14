<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class LineChartWidget extends ChartWidget
{
    protected ?string $heading = 'New User Registrations';

    public function getData(): array
    {
        $months = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->format('M Y');
            $months[] = $label;

            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $counts[] = $count;
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $counts,
                    'fill' => true,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor' => '#f59e0b',
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
