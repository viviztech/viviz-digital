<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CreatorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('creator')
            ->path('creator')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Purple,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->brandName(\App\Models\Setting::get('site_name', 'AuraAssets') . ' Creator Studio')
            ->brandLogo(null)
            ->darkMode(true, true)
            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Creator/Resources'), for: 'App\\Filament\\Creator\\Resources')
            ->discoverPages(in: app_path('Filament/Creator/Pages'), for: 'App\\Filament\\Creator\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Creator/Widgets'), for: 'App\\Filament\\Creator\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Creator\Widgets\DailyEarningsChart::class,
                \App\Filament\Creator\Widgets\StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->navigationGroups([
                'Products',
                'Orders',
                'Analytics',
                'Settings',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->userMenuItems([
                'admin-dashboard' => \Filament\Navigation\MenuItem::make()
                    ->label('Admin Dashboard')
                    ->url('/admin')
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn(): bool => auth()->user()?->is_admin ?? false),
            ]);
    }
}
