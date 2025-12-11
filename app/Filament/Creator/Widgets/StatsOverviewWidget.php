<?php

namespace App\Filament\Creator\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $shop = Auth::user()?->shop;

        if (!$shop) {
            return [
                Stat::make('Total Products', 0)
                    ->description('Upload your first product')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('gray'),
                Stat::make('Total Revenue*', \App\Models\Setting::get('currency_symbol', '$') . '0.00')
                    ->description('Start earning today')
                    ->color('gray'),
                Stat::make('Total Downloads', 0)
                    ->description('No downloads yet')
                    ->color('gray'),
            ];
        }

        // Get stats for the current shop
        $productCount = Product::where('shop_id', $shop->id)->count();
        $activeProducts = Product::where('shop_id', $shop->id)->where('is_active', true)->count();

        $totalRevenue = Order::query()
            ->whereHas('product', fn($q) => $q->where('shop_id', $shop->id))
            ->where('status', 'completed')
            ->sum('vendor_amount');

        $monthlyRevenue = Order::query()
            ->whereHas('product', fn($q) => $q->where('shop_id', $shop->id))
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('vendor_amount');

        $previousMonthRevenue = Order::query()
            ->whereHas('product', fn($q) => $q->where('shop_id', $shop->id))
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('vendor_amount');

        $totalDownloads = Product::where('shop_id', $shop->id)->sum('downloads_count');

        // Calculate revenue trend
        $revenueTrend = $previousMonthRevenue > 0
            ? round((($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : ($monthlyRevenue > 0 ? 100 : 0);

        return [
            Stat::make('Active Products', $activeProducts . ' / ' . $productCount)
                ->description($productCount . ' total products')
                ->descriptionIcon('heroicon-m-photo')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->color('primary'),

            Stat::make('Total Revenue*', \App\Models\Setting::get('currency_symbol', '$') . number_format($totalRevenue / 100, 2))
                ->description(($revenueTrend >= 0 ? '+' : '') . $revenueTrend . '% from last month')
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([2, 4, 6, 8, 5, 7, 9, 10])
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Total Downloads', number_format($totalDownloads))
                ->description('Across all products')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->chart([3, 5, 4, 7, 6, 8, 9, 7])
                ->color('info'),
        ];
    }
}
