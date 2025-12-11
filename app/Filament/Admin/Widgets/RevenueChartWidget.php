<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Platform Revenue (Last 30 Days)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $platformFees = [];
        $vendorPayouts = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $dayOrders = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->selectRaw('SUM(platform_fee) as fees, SUM(vendor_amount) as payouts')
                ->first();

            $platformFees[] = round(($dayOrders->fees ?? 0) / 100, 2);
            $vendorPayouts[] = round(($dayOrders->payouts ?? 0) / 100, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Platform Fees',
                    'data' => $platformFees,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Vendor Payouts',
                    'data' => $vendorPayouts,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
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
                    'display' => true,
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
