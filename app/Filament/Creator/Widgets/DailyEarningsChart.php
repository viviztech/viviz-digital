<?php

namespace App\Filament\Creator\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyEarningsChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Earnings';

    protected static ?string $description = 'Your revenue over the last 30 days';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $shop = Auth::user()?->shop;

        if (!$shop) {
            return [
                'datasets' => [
                    [
                        'label' => 'Earnings (' . \App\Models\Setting::get('currency_symbol', '$') . ')',
                        'data' => array_fill(0, 30, 0),
                        'borderColor' => 'rgb(147, 51, 234)',
                        'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $this->getLast30DaysLabels(),
            ];
        }

        // Get daily earnings for the last 30 days
        $earnings = Order::query()
            ->whereHas('product', function ($query) use ($shop) {
                $query->where('shop_id', $shop->id);
            })
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(vendor_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $data[] = isset($earnings[$date]) ? round($earnings[$date] / 100, 2) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Earnings (' . \App\Models\Setting::get('currency_symbol', '$') . ')',
                    'data' => $data,
                    'borderColor' => 'rgb(147, 51, 234)',
                    'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $this->getLast30DaysLabels(),
        ];
    }

    protected function getLast30DaysLabels(): array
    {
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('M d');
        }
        return $labels;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '" . \App\Models\Setting::get('currency_symbol', '$') . "' + value; }",
                    ],
                ],
            ],
        ];
    }
}
