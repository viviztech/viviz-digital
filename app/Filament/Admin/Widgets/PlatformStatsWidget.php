<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalVendors = User::where('is_vendor', true)->count();
        $totalShops = Shop::count();
        $verifiedShops = Shop::where('is_verified', true)->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();

        // Revenue calculations
        $totalRevenue = Order::where('status', 'completed')->sum('amount');
        $platformRevenue = Order::where('status', 'completed')->sum('platform_fee');
        $monthlyRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('platform_fee');

        $pendingOrders = Order::where('status', 'pending')->count();

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description($totalVendors . ' vendors')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->color('primary'),

            Stat::make('Active Shops', $totalShops)
                ->description($verifiedShops . ' verified')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->chart([3, 5, 4, 7, 6, 8, 9, 7])
                ->color('success'),

            Stat::make('Products', number_format($totalProducts))
                ->description($activeProducts . ' active')
                ->descriptionIcon('heroicon-m-photo')
                ->chart([2, 4, 6, 8, 5, 7, 9, 10])
                ->color('info'),

            Stat::make('Platform Revenue', \App\Models\Setting::get('currency_symbol', '$') . number_format($platformRevenue / 100, 2))
                ->description(\App\Models\Setting::get('currency_symbol', '$') . number_format($monthlyRevenue / 100, 2) . ' this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([5, 8, 4, 6, 9, 7, 10, 12])
                ->color('success'),

            Stat::make('Total GMV', \App\Models\Setting::get('currency_symbol', '$') . number_format($totalRevenue / 100, 2))
                ->description('Gross merchandise value')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Requires attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'gray'),
        ];
    }
}
